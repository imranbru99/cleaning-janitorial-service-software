<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class InspectionAnswer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'inspection_id', 'inspection_question_id', 'score', 'passed', 'notes',
    ];

    protected function casts(): array
    {
        return ['score' => 'decimal:2', 'passed' => 'boolean'];
    }
}
