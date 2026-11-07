<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'subscription_plan_id', 'status', 'billing_period',
        'starts_at', 'ends_at', 'trial_ends_at', 'cancelled_at', 'grace_ends_at', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'grace_ends_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
