<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PayrollRecord extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'period', 'period_start', 'period_end', 'base_pay',
        'overtime_pay', 'allowances', 'deductions', 'bonuses', 'net_pay', 'status', 'breakdown',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'base_pay' => 'decimal:2',
            'overtime_pay' => 'decimal:2',
            'allowances' => 'decimal:2',
            'deductions' => 'decimal:2',
            'bonuses' => 'decimal:2',
            'net_pay' => 'decimal:2',
            'breakdown' => 'array',
        ];
    }
}
