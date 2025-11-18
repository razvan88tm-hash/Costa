<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class SyncLog extends Model
{
    protected $fillable = [
        'type',
        'level',
        'project_id',
        'file_id',
        'file_name',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
