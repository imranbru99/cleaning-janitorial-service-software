<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use BelongsToTenant, BelongsToBranch, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'branch_id', 'user_id', 'type', 'name', 'company_name', 'email',
        'phone', 'status', 'segment', 'source', 'tags', 'notes', 'lifetime_value',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'lifetime_value' => 'decimal:2',
        ];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
