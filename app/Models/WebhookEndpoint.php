<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'url', 'secret', 'events', 'is_active'];

    protected function casts(): array
    {
        return ['events' => 'array', 'is_active' => 'boolean'];
    }
}
