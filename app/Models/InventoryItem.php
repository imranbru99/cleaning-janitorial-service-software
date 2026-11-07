<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'sku', 'name', 'category', 'unit', 'quantity_on_hand', 'reorder_level',
        'unit_cost', 'expiry_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'decimal:3',
            'reorder_level' => 'decimal:3',
            'unit_cost' => 'decimal:2',
            'expiry_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function isLowStock(): bool
    {
        return $this->quantity_on_hand <= $this->reorder_level;
    }
}
