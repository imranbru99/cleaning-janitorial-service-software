<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'invoice_id', 'description', 'quantity', 'unit_price', 'tax_rate', 'line_total',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }
}
