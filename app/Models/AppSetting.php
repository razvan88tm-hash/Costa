<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'monthly_salary',
        'per_diem',
        'hotel_night',
        'transport_per_km',
        'drive_projects_folder_id',
        'doc_ai_processor_id',
    ];

    /**
     * Singleton de setări globale.
     * Creează rândul dacă nu există.
     */
    public static function getSingleton(): self
    {
        return self::firstOrCreate([], [
            'monthly_salary'           => 4000,
            'per_diem'                 => 60,
            'hotel_night'              => 100,
            'transport_per_km'         => 0.80,
            'drive_projects_folder_id' => null,
            // aici poți pune un default, dar realist îl setezi din Tinker:
            'doc_ai_processor_id'      =>null,
        ]);
    }

    /**
     * Calculează tariful orar din salariul lunar (presupunem 160 ore / lună).
     */
    public function getHourlyRateAttribute(): float
    {
        if (! $this->monthly_salary) {
            return 0;
        }

        return $this->monthly_salary / 160;
    }
}
