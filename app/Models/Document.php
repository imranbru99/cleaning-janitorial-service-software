<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'tenant_id', 'title', 'disk', 'path', 'category', 'documentable_type',
        'documentable_id', 'expires_at', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return ['expires_at' => 'date'];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
