<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['customer_name', 'company', 'quote', 'rating', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }
}
