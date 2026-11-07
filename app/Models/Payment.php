<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'invoice_id', 'customer_id', 'reference', 'method', 'gateway',
        'gateway_transaction_id', 'status', 'amount', 'currency', 'paid_at', 'gateway_payload', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'gateway_payload' => 'array',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
