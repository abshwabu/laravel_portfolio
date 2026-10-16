<?php

namespace App\Support;

class Asset
{
    /**
     * In-memory cache for asset hashes during the request lifecycle.
     */
    protected static array $manifest = [];

    /**
     * Get content-hashed asset URL (e.g. css/main.a1b2c3d4.css).
     *
     * Ensures physical hashed file exists on disk for zero-overhead static serving.
     */
    public static function hashed(string $path): string
    {
        $cleanPath = ltrim(preg_replace('/\?.*$/', '', $path), '/');

        if (isset(static::$manifest[$cleanPath])) {
            return asset(static::$manifest[$cleanPath]);
        }

        $fullPath = public_path($cleanPath);

        if (! file_exists($fullPath)) {
            return asset($cleanPath);
        }

        $ext = pathinfo($cleanPath, PATHINFO_EXTENSION);
        $name = pathinfo($cleanPath, PATHINFO_FILENAME);
        $dir = pathinfo($cleanPath, PATHINFO_DIRNAME);
        $dirPrefix = ($dir === '.' || $dir === '') ? '' : $dir . '/';

        $content = file_get_contents($fullPath);
        if ($ext === 'css') {
            $content = static::minifyCss($content);
        } elseif ($ext === 'js') {
            $content = static::minifyJs($content, $fullPath);
        }

        $hash = substr(md5($content), 0, 8);
        $hashedRelativePath = "{$dirPrefix}{$name}.{$hash}.{$ext}";
        static::$manifest[$cleanPath] = $hashedRelativePath;

        // Ensure physical file exists on disk
        $hashedFullPath = public_path($hashedRelativePath);
        if (! file_exists($hashedFullPath)) {
            file_put_contents($hashedFullPath, $content);
        }

        return asset($hashedRelativePath);
    }

    /**
     * Native CSS minification.
     */
    public static function minifyCss(string $css): string
    {
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = preg_replace('/\s*([{}|:;,>+~])\s*/', '$1', $css);
        $css = preg_replace('/;+/', ';', $css);
        $css = str_replace(';}', '}', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        return trim($css);
    }

    /**
     * JavaScript minification with terser support when available, and native fallback.
     */
    public static function minifyJs(string $js, ?string $filePath = null): string
    {
        if ($filePath && file_exists($filePath)) {
            $escaped = escapeshellarg($filePath);
            $output = [];
            $retCode = 1;
            @exec("npx --yes terser {$escaped} -c -m 2>/dev/null", $output, $retCode);
            if ($retCode === 0 && ! empty($output)) {
                return implode("\n", $output);
            }
        }

        $js = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $js);
        $lines = explode("\n", $js);
        $out = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '//')) {
                continue;
            }
            $out[] = $trimmed;
        }

        return implode("\n", $out);
    }
}
