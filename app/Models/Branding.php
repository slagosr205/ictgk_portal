<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branding extends Model
{
    protected $table = 'brandings';

    protected $fillable = [
        'key',
        'name',
        'is_active',
        'assets',
        'palette',
        'texts',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'assets' => 'array',
        'palette' => 'array',
        'texts' => 'array',
    ];
}
