<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionTemplate extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'description', 'is_active', 'scoring_rules'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'scoring_rules' => 'array'];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(InspectionCategory::class);
    }
}
