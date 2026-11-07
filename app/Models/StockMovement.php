<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'inventory_item_id', 'warehouse_id', 'type', 'quantity',
        'reference_type', 'reference_id', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:3'];
    }
}
