<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class HeroImageService
{
    /**
     * Target responsive widths for the hero portrait.
     * Desktop renders at ~450px wide, mobile at ~352px wide.
     */
    public const RESPONSIVE_WIDTHS = [400, 700];

    /**
     * Maximum intrinsic width (2x Retina for ~450px container).
     */
    public const MAX_WIDTH = 700;

    /**
     * Quality settings for optimal compression without visual degradation.
     */
    public const AVIF_QUALITY = 70;
    public const WEBP_QUALITY = 80;
    public const JPEG_QUALITY = 82;
    public const PNG_COMPRESSION = 8; // 0-9 for GD

    /**
     * Optimize an uploaded or newly provided hero image file.
     */
    public function optimize(
        UploadedFile|string $source,
        ?string $customName = null,
        bool $updateDb = true,
        bool $generateFavicons = true
    ): array {
        $sourcePath = $source instanceof UploadedFile ? $source->getRealPath() : $source;

        if (! file_exists($sourcePath)) {
            throw new InvalidArgumentException("Source file does not exist: {$sourcePath}");
        }

        $extension = strtolower(pathinfo($source instanceof UploadedFile ? $source->getClientOriginalName() : $sourcePath, PATHINFO_EXTENSION));
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }
        if (! in_array($extension, ['png', 'jpg', 'webp', 'avif', 'gif'])) {
            $extension = 'png';
        }

        $baseName = $customName ?: (string) Str::ulid();
        $publicStorageDir = storage_path('app/public');
        $originalsDir = storage_path('app/originals/hero');

        File::ensureDirectoryExists($publicStorageDir);
        File::ensureDirectoryExists($originalsDir);

        // Rule 2: Keep master originals out of public/build output
        $originalBackup = "{$originalsDir}/{$baseName}_original.{$extension}";
        copy($sourcePath, $originalBackup);

        // Target fallback path in public storage
        $destFile = "{$publicStorageDir}/{$baseName}.{$extension}";
        if ($sourcePath !== $destFile) {
            copy($sourcePath, $destFile);
        }

        $results = $this->processImageVariants($destFile, $publicStorageDir, $baseName, $extension, $generateFavicons);

        if ($updateDb) {
            $setting = Setting::first();
            if ($setting) {
                // If previous image had a different base name, clean up old variants
                if ($setting->hero_gif && $setting->hero_gif !== "{$baseName}.{$extension}") {
                    $this->cleanupVariants($setting->hero_gif);
                }
                $setting->update(['hero_gif' => "{$baseName}.{$extension}"]);
            }
            Artisan::call('view:clear');
        }

        return $results;
    }

    /**
     * Optimize an existing image already saved in storage/app/public.
     */
    public function optimizeExisting(string $relativePath, bool $generateFavicons = true): array
    {
        $cleanPath = ltrim(preg_replace('/\?.*$/', '', $relativePath), '/');
        // Remove storage/ prefix if present
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }

        $fullPath = storage_path("app/public/{$cleanPath}");
        if (! file_exists($fullPath)) {
            throw new InvalidArgumentException("Existing file not found: {$fullPath}");
        }

        $dir = dirname($fullPath);
        $baseName = pathinfo($fullPath, PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // Backup master original
        $originalsDir = storage_path('app/originals/hero');
        File::ensureDirectoryExists($originalsDir);
        $originalBackup = "{$originalsDir}/{$baseName}_original.{$extension}";
        if (! file_exists($originalBackup)) {
            copy($fullPath, $originalBackup);
        }

        $results = $this->processImageVariants($fullPath, $dir, $baseName, $extension, $generateFavicons);
        Artisan::call('view:clear');

        return $results;
    }

    /**
     * Process all responsive AVIF, WebP, and fallback variants for the image.
     */
    protected function processImageVariants(
        string $srcFile,
        string $destDir,
        string $baseName,
        string $extension,
        bool $generateFavicons = true
    ): array {
        $img = $this->loadImage($srcFile);
        if (! $img) {
            throw new RuntimeException("Could not decode image at {$srcFile}");
        }

        $origW = imagesx($img);
        $origH = imagesy($img);
        $aspect = $origH / $origW;

        // Determine responsive widths (don't upscale smaller sources)
        $widths = [];
        foreach (self::RESPONSIVE_WIDTHS as $w) {
            if ($w <= $origW) {
                $widths[] = $w;
            }
        }
        if (empty($widths)) {
            $widths[] = min($origW, self::MAX_WIDTH);
        }
        // Always include largest allowed intrinsic width
        $maxWidth = min($origW, self::MAX_WIDTH);
        if (! in_array($maxWidth, $widths)) {
            $widths[] = $maxWidth;
        }
        sort($widths);

        $generated = [];

        foreach ($widths as $w) {
            $h = max(1, (int) round($w * $aspect));
            $resized = $this->resizeImage($img, $w, $h);

            // 1. AVIF variant
            $avifPath = "{$destDir}/{$baseName}-{$w}.avif";
            $this->saveAvif($resized, $avifPath, self::AVIF_QUALITY);
            $generated["avif_{$w}"] = [
                'path' => $avifPath,
                'width' => $w,
                'height' => $h,
                'size' => filesize($avifPath),
            ];

            // 2. WebP variant
            $webpPath = "{$destDir}/{$baseName}-{$w}.webp";
            $this->saveWebp($resized, $webpPath, self::WEBP_QUALITY);
            $generated["webp_{$w}"] = [
                'path' => $webpPath,
                'width' => $w,
                'height' => $h,
                'size' => filesize($webpPath),
            ];

            // If this is the max/default width, create root .avif and .webp
            if ($w === $maxWidth) {
                copy($avifPath, "{$destDir}/{$baseName}.avif");
                copy($webpPath, "{$destDir}/{$baseName}.webp");
            }
        }

        // 3. Fallback optimized raster (JPEG/PNG) at max displayed width
        $fallbackH = max(1, (int) round($maxWidth * $aspect));
        $fallbackImg = $this->resizeImage($img, $maxWidth, $fallbackH);
        $fallbackPath = "{$destDir}/{$baseName}.{$extension}";
        $this->saveFallback($fallbackImg, $fallbackPath, $extension);
        $generated['fallback'] = [
            'path' => $fallbackPath,
            'width' => $maxWidth,
            'height' => $fallbackH,
            'size' => filesize($fallbackPath),
        ];

        // 4. Site favicons
        if ($generateFavicons) {
            $generated['favicons'] = $this->generateFavicons($img);
        }

        return [
            'base_name' => $baseName,
            'original_dimensions' => "{$origW}x{$origH}",
            'generated_widths' => $widths,
            'files' => $generated,
            'count' => count($generated),
        ];
    }

    /**
     * Load an image file into a GD resource preserving alpha channel.
     */
    protected function loadImage(string $filePath)
    {
        $info = getimagesize($filePath);
        if (! $info) {
            return false;
        }

        $mime = $info['mime'] ?? '';
        $img = match ($mime) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($filePath),
            'image/png' => imagecreatefrompng($filePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($filePath) : false,
            'image/avif' => function_exists('imagecreatefromavif') ? imagecreatefromavif($filePath) : false,
            'image/gif' => imagecreatefromgif($filePath),
            default => false,
        };

        if ($img) {
            imagealphablending($img, true);
            imagesavealpha($img, true);
        }

        return $img;
    }

    /**
     * High-quality bicubic resampling with alpha preservation.
     */
    protected function resizeImage($srcImage, int $targetWidth, int $targetHeight)
    {
        $origW = imagesx($srcImage);
        $origH = imagesy($srcImage);

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);

        // Fill with transparent canvas
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $target,
            $srcImage,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $origW, $origH
        );

        return $target;
    }

    /**
     * Save AVIF image using GD or ImageMagick fallback.
     */
    protected function saveAvif($gdImage, string $destPath, int $quality = 70): bool
    {
        if (function_exists('imageavif')) {
            $ok = @imageavif($gdImage, $destPath, $quality);
            if ($ok && file_exists($destPath) && filesize($destPath) > 0) {
                return true;
            }
        }

        // ImageMagick fallback if available
        $magick = exec('which magick 2>/dev/null');
        if ($magick) {
            $tmpPng = tempnam(sys_get_temp_dir(), 'hero_avif_') . '.png';
            imagepng($gdImage, $tmpPng, 8);
            @exec("magick {$tmpPng} -quality {$quality} {$destPath} 2>/dev/null");
            @unlink($tmpPng);
            if (file_exists($destPath) && filesize($destPath) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save WebP image using GD.
     */
    protected function saveWebp($gdImage, string $destPath, int $quality = 80): bool
    {
        if (function_exists('imagewebp')) {
            return @imagewebp($gdImage, $destPath, $quality);
        }

        return false;
    }

    /**
     * Save fallback JPEG or PNG.
     */
    protected function saveFallback($gdImage, string $destPath, string $extension): bool
    {
        if (in_array(strtolower($extension), ['jpg', 'jpeg'])) {
            return @imagejpeg($gdImage, $destPath, self::JPEG_QUALITY);
        }

        return @imagepng($gdImage, $destPath, self::PNG_COMPRESSION);
    }

    /**
     * Generate responsive favicons and apple-touch-icon.
     */
    public function generateFavicons($srcImage): array
    {
        $publicDir = public_path();
        File::ensureDirectoryExists($publicDir);

        $favicons = [];

        // 1. apple-touch-icon.png (180x180)
        $appleImg = $this->resizeImage($srcImage, 180, 180);
        $applePath = "{$publicDir}/apple-touch-icon.png";
        imagepng($appleImg, $applePath, 9);
        $favicons['apple-touch-icon'] = $applePath;

        // 2. favicon-32x32.png (32x32)
        $f32Img = $this->resizeImage($srcImage, 32, 32);
        $f32Path = "{$publicDir}/favicon-32x32.png";
        imagepng($f32Img, $f32Path, 9);
        $favicons['favicon-32'] = $f32Path;

        // 3. favicon-16x16.png (16x16)
        $f16Img = $this->resizeImage($srcImage, 16, 16);
        $f16Path = "{$publicDir}/favicon-16x16.png";
        imagepng($f16Img, $f16Path, 9);
        $favicons['favicon-16'] = $f16Path;

        // 4. favicon.ico (32x32 Windows ICO format)
        ob_start();
        imagepng($f32Img, null, 9);
        $f32PngBytes = ob_get_clean();

        $icoHeader = pack('v3', 0, 1, 1);
        $icoEntry = pack('C4v2V2', 32, 32, 0, 0, 1, 32, strlen($f32PngBytes), 22);
        $icoData = $icoHeader . $icoEntry . $f32PngBytes;
        $icoPath = "{$publicDir}/favicon.ico";
        file_put_contents($icoPath, $icoData);
        $favicons['favicon.ico'] = $icoPath;

        return $favicons;
    }

    /**
     * Clean up previous hero variant files.
     */
    protected function cleanupVariants(string $oldRelativePath): void
    {
        $baseName = pathinfo($oldRelativePath, PATHINFO_FILENAME);
        if (! $baseName) {
            return;
        }

        $dir = storage_path('app/public/' . dirname($oldRelativePath));
        if (! is_dir($dir)) {
            return;
        }

        foreach (glob("{$dir}/{$baseName}*") as $file) {
            // Don't delete if it's the exact current file
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}
