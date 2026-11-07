<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'branch_id', 'name', 'asset_tag', 'serial_number', 'category', 'status',
        'assigned_employee_id', 'purchase_date', 'purchase_cost', 'warranty_expires_at', 'next_maintenance_at',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_cost' => 'decimal:2',
            'warranty_expires_at' => 'date',
            'next_maintenance_at' => 'date',
        ];
    }
}
