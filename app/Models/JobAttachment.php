<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class JobAttachment extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'cleaning_job_id', 'type', 'path', 'caption', 'uploaded_by'];
}
