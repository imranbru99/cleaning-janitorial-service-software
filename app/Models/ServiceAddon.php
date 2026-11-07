<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ServiceAddon extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'service_id', 'name', 'price', 'duration_minutes', 'is_active'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_active' => 'boolean'];
    }
}
