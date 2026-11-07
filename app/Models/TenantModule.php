<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TenantModule extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'module_key', 'is_active', 'settings', 'activated_at', 'deactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
            'activated_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }
}
