<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class InspectionQuestion extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'inspection_category_id', 'question', 'type', 'max_score', 'weight', 'is_required', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
            'weight' => 'decimal:2',
            'is_required' => 'boolean',
        ];
    }
}
