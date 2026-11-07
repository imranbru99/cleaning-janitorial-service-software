<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'support_ticket_id', 'user_id', 'message', 'is_internal'];

    protected function casts(): array
    {
        return ['is_internal' => 'boolean'];
    }
}
