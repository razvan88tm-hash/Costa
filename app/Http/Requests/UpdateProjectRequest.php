<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // permite tuturor să actualizeze proiectele
    }

    public function rules(): array
    {
        return [
            'project_number' => 'required|string|max:50',
            'name'           => 'required|string|max:255',
            'client_name'    => 'nullable|string|max:255',
            'overall_rag'    => 'nullable|string|in:Green,Yellow,Red',
            'drive_folder_url' => 'nullable|string|max:255',
            'layout_file_url'  => 'nullable|string|max:255',
            'offers_folder_url' => 'nullable|string|max:255',
            'sell_price'       => 'nullable|numeric',
        ];
    }
}
