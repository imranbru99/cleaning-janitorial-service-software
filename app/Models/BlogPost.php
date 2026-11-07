<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class BlogPost extends Model
{
    use HasUniversalSlug;

    protected $fillable = [
        'slug', 'title', 'locale', 'excerpt', 'content', 'author', 'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'published_at' => 'datetime'];
    }

    public function getSlugOptions(): \ImranDev\UniversalSlug\SlugOptions
    {
        return \ImranDev\UniversalSlug\SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }
}
