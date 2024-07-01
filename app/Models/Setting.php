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
}
