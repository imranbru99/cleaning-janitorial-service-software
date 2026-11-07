<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAssignment extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'cleaning_job_id', 'employee_id', 'role', 'assigned_at'];

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function cleaningJob(): BelongsTo
    {
        return $this->belongsTo(CleaningJob::class);
    }
}
