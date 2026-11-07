<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'tenant_id', 'user_id', 'feature', 'provider', 'model', 'tokens_in', 'tokens_out', 'cost_usd', 'status',
    ];

    protected function casts(): array
    {
        return ['cost_usd' => 'decimal:6'];
    }
}
