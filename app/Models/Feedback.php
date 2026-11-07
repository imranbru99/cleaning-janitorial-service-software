<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use BelongsToTenant;

    protected $table = 'feedback';

    protected $fillable = ['tenant_id', 'customer_id', 'cleaning_job_id', 'rating', 'comments'];
}
