<?php

namespace App\Enums;

enum InspectionResult: string
{
    case Excellent = 'excellent';
    case Good = 'good';
    case NeedsImprovement = 'needs_improvement';
    case Failed = 'failed';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Excellent => 'Excellent',
            self::Good => 'Good',
            self::NeedsImprovement => 'Needs Improvement',
            self::Failed => 'Failed',
            self::Pending => 'Pending',
        };
    }

    public static function fromScore(float $score, array $thresholds = []): self
    {
        $thresholds = array_merge(config('cleaning.inspection_thresholds', []), $thresholds);

        if ($score >= ($thresholds['excellent'] ?? 90)) {
            return self::Excellent;
        }
        if ($score >= ($thresholds['good'] ?? 80)) {
            return self::Good;
        }
        if ($score >= ($thresholds['needs_improvement'] ?? 70)) {
            return self::NeedsImprovement;
        }

        return self::Failed;
    }
}
