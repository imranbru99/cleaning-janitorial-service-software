<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class CmsPage extends Model
{
    use HasUniversalSlug;

    protected $fillable = [
        'slug', 'title', 'locale', 'content', 'meta_title', 'meta_description', 'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function getSlugOptions(): \ImranDev\UniversalSlug\SlugOptions
    {
        return \ImranDev\UniversalSlug\SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }
}
