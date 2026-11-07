<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class JobTask extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'cleaning_job_id', 'title', 'is_completed', 'sort_order'];

    protected function casts(): array
    {
        return ['is_completed' => 'boolean'];
    }
}
