<?php

namespace App\Enums;

enum JobStatus: string
{
    case Scheduled = 'scheduled';
    case Assigned = 'assigned';
    case EnRoute = 'en_route';
    case InProgress = 'in_progress';
    case Paused = 'paused';
    case Completed = 'completed';
    case AwaitingInspection = 'awaiting_inspection';
    case Approved = 'approved';
    case ReworkRequired = 'rework_required';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
