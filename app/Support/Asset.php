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

        $hash = substr(md5_file($fullPath), 0, 8);
        $ext = pathinfo($cleanPath, PATHINFO_EXTENSION);
        $name = pathinfo($cleanPath, PATHINFO_FILENAME);
        $dir = pathinfo($cleanPath, PATHINFO_DIRNAME);
        $dirPrefix = ($dir === '.' || $dir === '') ? '' : $dir . '/';

        $hashedRelativePath = "{$dirPrefix}{$name}.{$hash}.{$ext}";
        static::$manifest[$cleanPath] = $hashedRelativePath;

        // Ensure physical file exists on disk
        $hashedFullPath = public_path($hashedRelativePath);
        if (! file_exists($hashedFullPath)) {
            @copy($fullPath, $hashedFullPath);
        }

        return asset($hashedRelativePath);
    }
}
