<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Service extends Model
{
    use BelongsToTenant, SoftDeletes, HasUniversalSlug;

    protected $fillable = [
        'tenant_id', 'service_category_id', 'name', 'slug', 'description', 'pricing_method',
        'base_price', 'hourly_rate', 'per_sqft_rate', 'duration_minutes', 'required_skills',
        'required_equipment', 'checklist', 'is_recurring_eligible', 'is_active', 'is_public', 'tax_rate',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'per_sqft_rate' => 'decimal:4',
            'tax_rate' => 'decimal:2',
            'required_skills' => 'array',
            'required_equipment' => 'array',
            'checklist' => 'array',
            'is_recurring_eligible' => 'boolean',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ServiceAddon::class);
    }

    public function getSlugOptions(): \ImranDev\UniversalSlug\SlugOptions
    {
        return \ImranDev\UniversalSlug\SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
}
