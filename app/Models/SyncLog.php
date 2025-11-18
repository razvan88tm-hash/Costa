<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'status',
        'source',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];
}
