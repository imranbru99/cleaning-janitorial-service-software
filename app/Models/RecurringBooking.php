<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class RecurringBooking extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'customer_id', 'service_id', 'frequency', 'interval', 'start_date',
        'end_date', 'preferred_time', 'is_active', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }
}
