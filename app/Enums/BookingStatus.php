<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Inquiry = 'inquiry';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Paused = 'paused';
    case Completed = 'completed';
    case AwaitingInspection = 'awaiting_inspection';
    case Approved = 'approved';
    case ReworkRequired = 'rework_required';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Inquiry => 'Inquiry',
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Assigned => 'Assigned',
            self::InProgress => 'In Progress',
            self::Paused => 'Paused',
            self::Completed => 'Completed',
            self::AwaitingInspection => 'Awaiting Inspection',
            self::Approved => 'Approved',
            self::ReworkRequired => 'Rework Required',
            self::Cancelled => 'Cancelled',
            self::NoShow => 'No Show',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Inquiry, self::Pending => 'gray',
            self::Confirmed, self::Assigned => 'info',
            self::InProgress, self::Paused => 'warning',
            self::Completed, self::Approved => 'success',
            self::AwaitingInspection => 'primary',
            self::ReworkRequired, self::Cancelled, self::NoShow => 'danger',
        };
    }
}
