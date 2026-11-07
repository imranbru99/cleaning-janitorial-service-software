<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'slug', 'description', 'price', 'service_ids', 'is_active'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'service_ids' => 'array', 'is_active' => 'boolean'];
    }
}
