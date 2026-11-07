<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Trialing = 'trialing';
    case Active = 'active';
    case PastDue = 'past_due';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }

    public function isUsable(): bool
    {
        return in_array($this, [self::Trialing, self::Active, self::PastDue], true);
    }
}
