<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use BelongsToTenant, BelongsToBranch, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'branch_id', 'department_id', 'team_id', 'user_id', 'employee_number',
        'first_name', 'last_name', 'email', 'phone', 'employment_type', 'status', 'hire_date',
        'hourly_rate', 'salary', 'skills', 'availability', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'hourly_rate' => 'decimal:2',
            'salary' => 'decimal:2',
            'skills' => 'array',
            'availability' => 'array',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(EmployeeCertification::class);
    }
}
