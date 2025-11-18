<?php

namespace App\Http\Controllers;

use App\Models\AppSetting; // Asigură-te că folosești calea corectă
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Afișează formularul de setări.
     */
    public function edit()
    {
        // Folosim funcția 'current' din modelul AppSetting
        $settings = AppSetting::getSingleton();
        return view('settings.edit', compact('settings'));
    }

    /**
     * Actualizează setările în baza de date.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'monthly_salary'     => 'required|numeric|min:0',
            'per_diem'           => 'required|numeric|min:0',
            'hotel_night'        => 'required|numeric|min:0',
            'transport_per_km'   => 'required|numeric|min:0',
            'drive_projects_folder_id' => 'nullable|string|max:255', // Câmpul tău nou
            'doc_ai_processor_id' => 'nullable|string|max:255',
        ]);

        $settings = AppSetting::getSingleton();
        $settings->update($validated);

        return redirect()->back()->with('success', 'Setările globale au fost actualizate!');
    }
}