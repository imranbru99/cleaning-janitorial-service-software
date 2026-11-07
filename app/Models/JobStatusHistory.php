<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class JobStatusHistory extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'cleaning_job_id', 'from_status', 'to_status', 'changed_by', 'notes'];
}
