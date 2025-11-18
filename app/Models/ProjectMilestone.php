<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'type',
        'name',
        'description',
        'planned_date',
        'actual_date',
        'status',
        'sort_order',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
