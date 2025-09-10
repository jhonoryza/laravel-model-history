<?php

namespace Jhonoryza\LaravelModelHistory\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseLog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];
}
