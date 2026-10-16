<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "image",
        "images",
        "url",
        "keyword",
        "description",
        "github_url",
    ];

    protected $casts = [
        'images' => 'array',
    ];

    /**
     * Get all images (primary cover image first, followed by additional gallery images).
     */
    public function getAllImagesAttribute(): array
    {
        $all = [];
        if (! empty($this->image)) {
            $all[] = $this->image;
        }

        if (is_array($this->images)) {
            foreach ($this->images as $img) {
                if (! empty($img) && ! in_array($img, $all)) {
                    $all[] = $img;
                }
            }
        }

        return array_values($all);
    }

    /**
     * Resolve optimized AVIF, WebP, and thumbnail source data for all gallery images.
     */
    public function getAllImagesDataAttribute(): array
    {
        $service = app(\App\Services\ProjectImageService::class);
        $data = [];

        foreach ($this->all_images as $index => $imgPath) {
            $imgData = $service->getOptimizedImageData($imgPath);
            $imgData['alt'] = "{$this->title} - Screenshot " . ($index + 1);
            $data[] = $imgData;
        }

        return $data;
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saved(function (Project $project) {
            try {
                app(\App\Services\ProjectImageService::class)->optimizeProjectImages($project);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Auto-optimizing project #{$project->id} images failed: " . $e->getMessage());
            }
        });
    }
}
