<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class CorrectiveAction extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'inspection_id', 'cleaning_job_id', 'title', 'description', 'status',
        'assigned_to', 'due_at', 'completed_at', 'reinspection_id',
    ];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}
