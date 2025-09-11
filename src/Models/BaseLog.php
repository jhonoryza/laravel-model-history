<?php

namespace Jhonoryza\ModelHistory\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'model_id',
        'operation',
        'message',
        'operation',
        'changed_by_model',
        'changed_by',
        'old_data',
        'new_data',
        'created_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];
}
