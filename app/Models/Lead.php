<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'branch_id', 'assigned_to', 'name', 'email', 'phone', 'company',
        'source', 'status', 'estimated_value', 'notes', 'converted_at', 'customer_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'estimated_value' => 'decimal:2',
            'converted_at' => 'datetime',
        ];
    }
}
