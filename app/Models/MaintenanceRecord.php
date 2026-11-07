<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'asset_id', 'title', 'description', 'cost', 'performed_at', 'next_due_at',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'performed_at' => 'date',
            'next_due_at' => 'date',
        ];
    }
}
