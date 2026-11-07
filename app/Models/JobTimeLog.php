<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class JobTimeLog extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'cleaning_job_id', 'employee_id', 'action', 'logged_at', 'notes'];

    protected function casts(): array
    {
        return ['logged_at' => 'datetime'];
    }
}
