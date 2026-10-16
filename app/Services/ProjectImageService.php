<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class ProjectImageService
{
    /**
     * Target responsive widths for project previews and gallery screenshots.
     */
    public const RESPONSIVE_WIDTHS = [400, 800, 1200];

    /**
     * Maximum intrinsic width.
     */
    public const MAX_WIDTH = 1200;

    /**
     * Quality settings.
     */
    public const AVIF_QUALITY = 70;
    public const WEBP_QUALITY = 80;
    public const JPEG_QUALITY = 82;
    public const PNG_COMPRESSION = 8; // 0-9

    /**
     * Optimize all images for a given Project model.
     */
    public function optimizeProjectImages(Project $project, bool $force = false): array
    {
        $allImages = $project->all_images;
        $results = [];

        foreach ($allImages as $imgPath) {
            if (empty($imgPath)) {
                continue;
            }
            try {
                $results[$imgPath] = $this->optimizeSingleImage($imgPath, $force);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Failed optimizing project image {$imgPath}: " . $e->getMessage());
            }
        }

        if (! empty($results)) {
            Artisan::call('view:clear');
        }

        return $results;
    }

    /**
     * Optimize a single project image path or uploaded file.
     */
    public function optimizeSingleImage(UploadedFile|string $source, bool $force = false): array
    {
        if ($source instanceof UploadedFile) {
            $sourcePath = $source->getRealPath();
            $extension = strtolower($source->getClientOriginalExtension());
            $baseName = (string) Str::ulid();
            $destDir = storage_path('app/public/projects');
        } else {
            $cleanPath = ltrim(preg_replace('/\?.*$/', '', $source), '/');
            if (str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = substr($cleanPath, 8);
            }

            $sourcePath = storage_path("app/public/{$cleanPath}");
            if (! file_exists($sourcePath)) {
                // Check if in public/img/project-preview/
                if (file_exists(public_path("img/project-preview/{$cleanPath}"))) {
                    $sourcePath = public_path("img/project-preview/{$cleanPath}");
                } else {
                    throw new InvalidArgumentException("Project image not found: {$sourcePath}");
                }
            }

            $destDir = dirname($sourcePath);
            $baseName = pathinfo($sourcePath, PATHINFO_FILENAME);
            $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        }

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }
        if (! in_array($extension, ['png', 'jpg', 'webp', 'avif', 'gif'])) {
            $extension = 'png';
        }

        File::ensureDirectoryExists($destDir);

        // Check if already optimized and force is false
        if (! $force && file_exists("{$destDir}/{$baseName}-400.avif") && file_exists("{$destDir}/{$baseName}-400.webp")) {
            return [
                'base_name' => $baseName,
                'status' => 'already_optimized',
            ];
        }

        // Backup master original in storage/app/originals/projects
        $originalsDir = storage_path('app/originals/projects');
        File::ensureDirectoryExists($originalsDir);
        $originalBackup = "{$originalsDir}/{$baseName}_original.{$extension}";
        if (! file_exists($originalBackup) && file_exists($sourcePath)) {
            copy($sourcePath, $originalBackup);
        }

        return $this->processVariants($sourcePath, $destDir, $baseName, $extension);
    }

    /**
     * Process all responsive AVIF, WebP, and fallback variants.
     */
    protected function processVariants(string $srcFile, string $destDir, string $baseName, string $extension): array
    {
        $img = $this->loadImage($srcFile);
        if (! $img) {
            throw new RuntimeException("Could not decode image at {$srcFile}");
        }

        $origW = imagesx($img);
        $origH = imagesy($img);
        $aspect = $origH / $origW;

        $widths = [];
        foreach (self::RESPONSIVE_WIDTHS as $w) {
            if ($w <= $origW) {
                $widths[] = $w;
            }
        }
        if (empty($widths)) {
            $widths[] = min($origW, self::MAX_WIDTH);
        }
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

            // If this is default width (~800px or closest), create root .avif and .webp
            $defaultWidth = min(800, $maxWidth);
            if ($w === $defaultWidth || ($w === $maxWidth && ! isset($generated['root_avif']))) {
                copy($avifPath, "{$destDir}/{$baseName}.avif");
                copy($webpPath, "{$destDir}/{$baseName}.webp");
                $generated['root_avif'] = true;
            }
        }

        // 3. Fallback optimized raster
        $fallbackW = min($origW, 1200);
        $fallbackH = max(1, (int) round($fallbackW * $aspect));
        $fallbackImg = $this->resizeImage($img, $fallbackW, $fallbackH);
        $fallbackPath = "{$destDir}/{$baseName}.{$extension}";
        $this->saveFallback($fallbackImg, $fallbackPath, $extension);
        $generated['fallback'] = [
            'path' => $fallbackPath,
            'width' => $fallbackW,
            'height' => $fallbackH,
            'size' => filesize($fallbackPath),
        ];

        return [
            'base_name' => $baseName,
            'original_dimensions' => "{$origW}x{$origH}",
            'generated_widths' => $widths,
            'files' => $generated,
            'status' => 'optimized',
        ];
    }

    /**
     * Resolve responsive source data for frontend <picture> or modal carousel.
     */
    public function getOptimizedImageData(string $imagePath): array
    {
        $cleanPath = ltrim(preg_replace('/\?.*$/', '', $imagePath), '/');
        if (! str_starts_with($cleanPath, 'storage/') && ! str_starts_with($cleanPath, 'img/')) {
            if (file_exists(public_path('storage/' . $cleanPath))) {
                $cleanPath = 'storage/' . $cleanPath;
            } elseif (file_exists(public_path('img/project-preview/' . $cleanPath))) {
                $cleanPath = 'img/project-preview/' . $cleanPath;
            } elseif (file_exists(public_path('img/' . $cleanPath))) {
                $cleanPath = 'img/' . $cleanPath;
            } else {
                $cleanPath = 'storage/' . $cleanPath;
            }
        }

        $dir = pathinfo($cleanPath, PATHINFO_DIRNAME);
        $name = pathinfo($cleanPath, PATHINFO_FILENAME);
        $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));

        $avifSources = [];
        $webpSources = [];

        foreach (self::RESPONSIVE_WIDTHS as $w) {
            $avifRel = "{$dir}/{$name}-{$w}.avif";
            if (file_exists(public_path($avifRel))) {
                $avifSources[] = asset($avifRel) . " {$w}w";
            }
            $webpRel = "{$dir}/{$name}-{$w}.webp";
            if (file_exists(public_path($webpRel))) {
                $webpSources[] = asset($webpRel) . " {$w}w";
            }
        }

        // Thumb: 400w webp if available, or webp root, or fallback
        $thumbUrl = asset($cleanPath);
        if (file_exists(public_path("{$dir}/{$name}-400.webp"))) {
            $thumbUrl = asset("{$dir}/{$name}-400.webp");
        } elseif (file_exists(public_path("{$dir}/{$name}.webp"))) {
            $thumbUrl = asset("{$dir}/{$name}.webp");
        }

        // Default display image (800w webp/avif or fallback)
        $displayWebp = file_exists(public_path("{$dir}/{$name}-800.webp")) ? asset("{$dir}/{$name}-800.webp") : (file_exists(public_path("{$dir}/{$name}.webp")) ? asset("{$dir}/{$name}.webp") : null);
        $displayAvif = file_exists(public_path("{$dir}/{$name}-800.avif")) ? asset("{$dir}/{$name}-800.avif") : (file_exists(public_path("{$dir}/{$name}.avif")) ? asset("{$dir}/{$name}.avif") : null);

        return [
            'src' => asset($cleanPath),
            'thumb' => $thumbUrl,
            'avif_srcset' => implode(', ', $avifSources),
            'webp_srcset' => implode(', ', $webpSources),
            'display_avif' => $displayAvif,
            'display_webp' => $displayWebp,
            'name' => $name,
        ];
    }

    /**
     * Load image into a GD resource preserving alpha transparency.
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
     * Resample true color image with transparent background.
     */
    protected function resizeImage($srcImage, int $targetWidth, int $targetHeight)
    {
        $origW = imagesx($srcImage);
        $origH = imagesy($srcImage);

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);

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

        $magick = exec('which magick 2>/dev/null');
        if ($magick) {
            $tmpPng = tempnam(sys_get_temp_dir(), 'proj_avif_') . '.png';
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
}
