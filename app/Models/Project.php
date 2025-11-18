<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_number',
        'client_name',
        'client_contact_person', // NOU
        'client_phone',          // NOU
        'client_email',          // NOU
        'name',
        'number',
        'customer_site',
        'location',
        'pm_name',
        'overall_rag',
        'percent_complete',
        'start_date',
        'target_handover',
        'real_handover',
        'sell_price',
        'planned_budget',
        'drive_folder_url',
        'layout_file_url',
        'offers_folder_url',     // NOU
    ];

    // Relații
    public function costItems()
    {
        return $this->hasMany(ProjectCostItem::class);
    }

    public function workerLogs()
    {
        return $this->hasMany(ProjectWorkerLog::class);
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function risks()
    {
        return $this->hasMany(ProjectRisk::class);
    }

    // === CALCULE DIN COST ITEMS ===

    public function getTotalPlannedBudgetAttribute()
    {
        return $this->costItems->sum('planned_budget');
    }

    public function getTotalCommittedPoAttribute()
    {
        return $this->costItems->sum('committed_po');
    }

    public function getTotalActualCostAttribute()
    {
        // Suma introdusă manual (Cost Items)
        $manualSum = $this->costItems->sum('actual_cost');
        
        // Suma din facturile scanate (doar cele 'approved')
        $scannedSum = $this->invoices->where('status', 'approved')->sum('amount');

        return $manualSum + $scannedSum;
    
    }

    public function getTotalForecastToCompleteAttribute()
    {
        return $this->costItems->sum('forecast_to_complete');
    }

    // === CALCULE COSTURI MUNCITORI ===

    public function getTotalWorkerCostsAttribute()
    {
        return $this->workerLogs->sum('total_cost');
    }

    public function getAutoProfitAttribute()
    {
        if (is_null($this->sell_price)) {
            return null;
        }

        $totalMaterials = $this->total_actual_cost + $this->total_forecast_to_complete;
        $totalWorkers   = $this->total_worker_costs;

        return $this->sell_price - ($totalMaterials + $totalWorkers);
    }

    // === CALCULE IN MIELSTONES ===
    public function getCalculatedPercentCompleteAttribute()
    {
        if ($this->milestones->count() === 0) {
            // dacă nu ai milestones, folosești ce e în DB sau 0
            return $this->percent_complete ?? 0;
        }

        $total = $this->milestones->count();
        $done  = $this->milestones->where('status', 'done')->count();

        return round(($done / $total) * 100, 1); // ex: 62.5 %
    }

   public function invoices()
{
    return $this->hasMany(Invoice::class);
}

/**
 * Actual (Materials) = suma tuturor facturilor proiectului.
 * Poți filtra ulterior pe category == 'materials' dacă vrei.
 */
public function getActualMaterialsAttribute(): float
{
    // dacă vrei doar anumite categorii, folosește ->where('category', 'materials')
    return (float) $this->invoices()->sum('amount');
}


    
}

