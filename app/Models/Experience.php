<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        "role",
        "start_date",
        "end_date",
        "company",
        "company_url",
        "job_type",
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(ExperienceTask::class);
    }
}
