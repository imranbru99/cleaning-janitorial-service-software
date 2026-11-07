<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AutomationExecution extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'automation_rule_id', 'status', 'context', 'error', 'executed_at',
    ];

    protected function casts(): array
    {
        return ['context' => 'array', 'executed_at' => 'datetime'];
    }
}
