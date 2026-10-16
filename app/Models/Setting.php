<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        "username",
        "name",
        "title",
        "linkedin_url",
        "github_url",
        "instagram_url",
        "hero_gif",
        "email",
        "phone",
        "pronouns",
        "location",
        "languages",
        "hobbies",
    ];

    protected $casts =[
        'languages' => 'array',
        'hobbies' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saved(function (Setting $setting) {
            if ($setting->wasChanged('hero_gif') && ! empty($setting->hero_gif)) {
                try {
                    app(\App\Services\HeroImageService::class)->optimizeExisting($setting->hero_gif);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Auto-optimizing hero image failed on save: ' . $e->getMessage());
                }
            }
        });
    }
}
