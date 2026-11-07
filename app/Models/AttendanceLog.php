<?php

namespace App\Models;

use App\Enums\AttendanceMethod;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use BelongsToTenant;

    protected $table = 'workforce_attendance_logs';

    protected $fillable = [
        'tenant_id', 'employee_id', 'branch_id', 'work_date', 'checked_in_at', 'checked_out_at',
        'break_minutes', 'worked_hours', 'overtime_hours', 'method', 'status', 'latitude',
        'longitude', 'device_id', 'notes', 'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'worked_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'method' => AttendanceMethod::class,
            'approved_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
