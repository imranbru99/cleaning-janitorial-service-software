<?php

namespace App\Models;

use App\Enums\JobStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CleaningJob extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $table = 'cleaning_jobs';

    protected $fillable = [
        'tenant_id', 'branch_id', 'booking_id', 'customer_id', 'customer_address_id', 'reference',
        'title', 'status', 'scheduled_start', 'scheduled_end', 'started_at', 'completed_at',
        'checklist', 'checklist_progress', 'instructions', 'completion_notes',
        'customer_signature_path', 'supervisor_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => JobStatus::class,
            'scheduled_start' => 'datetime',
            'scheduled_end' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'checklist' => 'array',
            'checklist_progress' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(JobTask::class);
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(JobTimeLog::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }
}
