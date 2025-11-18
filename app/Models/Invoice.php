<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'project_id',
        'supplier',
        'invoice_number',
        'invoice_date',
        'due_date',
        'currency',
        'total_amount',
        'net_amount',
        'vat_amount',
        'drive_file_id',
        'file_name',
        'drive_file_url',
        'raw_json',
        // dacă există în DB:
        'category',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
