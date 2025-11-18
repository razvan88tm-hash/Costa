<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCostItem extends Model
{
    protected $fillable = [
        'project_id',
        'category',
        'line_item',
        'planned_budget',
        'committed_po',
        'actual_cost',
        'forecast_to_complete',
        'variance_act_plan',
        'percent_variance',
        'notes',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
