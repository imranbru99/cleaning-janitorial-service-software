<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionCategory extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'inspection_template_id', 'name', 'weight', 'sort_order'];

    protected function casts(): array
    {
        return ['weight' => 'decimal:2'];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(InspectionQuestion::class);
    }
}
