<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'branch_id', 'name', 'leader_id'];
}
