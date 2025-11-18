<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectWorkerLog extends Model
{
    protected $table = 'project_worker_logs';

    protected $fillable = [
        'project_id',
        'worker_name',
        'days_on_site',
        'hours_on_site',
        'transport_km',
        'per_diem_total',
        'hotel_total',
        'labor_cost',
        'transport_cost',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'days_on_site'   => 'integer',
        'hours_on_site'  => 'float',
        'transport_km'   => 'float',
        'per_diem_total' => 'float',
        'hotel_total'    => 'float',
        'labor_cost'     => 'float',
        'transport_cost' => 'float',
        'total_cost'     => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Umple modelul cu date brute și recalculează costurile în funcție de setări.
     */
    public function fillAndCalculate(array $data): self
    {
        $this->fill($data);

        /** @var \App\Models\AppSetting $settings */
        $settings = AppSetting::getSingleton();

        $days   = (int) ($this->days_on_site ?? 0);
        $hours  = (float) ($this->hours_on_site ?? 0);
        $km     = (float) ($this->transport_km ?? 0);

        $this->per_diem_total = $days * (float) $settings->per_diem;
        $this->hotel_total    = $days * (float) $settings->hotel_night;
        $this->labor_cost     = $hours * (float) $settings->hourly_rate;
        $this->transport_cost = $km   * (float) $settings->transport_per_km;

        $this->total_cost = $this->per_diem_total
            + $this->hotel_total
            + $this->labor_cost
            + $this->transport_cost;

        return $this;
    }
}
