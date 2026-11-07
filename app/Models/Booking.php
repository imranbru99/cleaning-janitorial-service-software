<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Traits\BelongsToBranch;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use BelongsToTenant, BelongsToBranch, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'branch_id', 'customer_id', 'customer_address_id', 'reference', 'status',
        'type', 'scheduled_start', 'scheduled_end', 'estimated_duration_minutes', 'subtotal',
        'tax_total', 'discount_total', 'total', 'deposit_amount', 'special_instructions',
        'preferences', 'created_by', 'assigned_supervisor_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'scheduled_start' => 'datetime',
            'scheduled_end' => 'datetime',
            'subtotal' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'total' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'preferences' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function cleaningJob(): HasOne
    {
        return $this->hasOne(CleaningJob::class);
    }
}
