<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'trigger', 'conditions', 'actions', 'delay_minutes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'actions' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
