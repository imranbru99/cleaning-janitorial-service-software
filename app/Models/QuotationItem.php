<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'quotation_id', 'service_id', 'description', 'quantity', 'unit_price', 'line_total',
    ];

    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2', 'line_total' => 'decimal:2'];
    }
}
