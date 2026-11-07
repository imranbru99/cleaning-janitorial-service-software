<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class EmployeeSkill extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'employee_id', 'skill', 'level'];
}
