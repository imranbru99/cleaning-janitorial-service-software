<?php

namespace App\Models;

use App\Enums\InspectionResult;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'cleaning_job_id', 'customer_id', 'inspection_template_id', 'inspector_id',
        'status', 'result', 'score', 'scheduled_at', 'completed_at', 'summary', 'customer_feedback',
    ];

    protected function casts(): array
    {
        return [
            'result' => InspectionResult::class,
            'score' => 'decimal:2',
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function answers(): HasMany
    {
        return $this->hasMany(InspectionAnswer::class);
    }

    public function correctiveActions(): HasMany
    {
        return $this->hasMany(CorrectiveAction::class);
    }

    public function cleaningJob(): BelongsTo
    {
        return $this->belongsTo(CleaningJob::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InspectionTemplate::class, 'inspection_template_id');
    }
}
