<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'key', 'name', 'description', 'version', 'category', 'is_core',
        'is_global_enabled', 'dependencies', 'permissions', 'settings_schema',
    ];

    protected function casts(): array
    {
        return [
            'is_core' => 'boolean',
            'is_global_enabled' => 'boolean',
            'dependencies' => 'array',
            'permissions' => 'array',
            'settings_schema' => 'array',
        ];
    }
}
