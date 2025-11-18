<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Formular creare factură pentru un proiect.
     */
    public function create(Project $project): View
    {
        return view('invoices.create', compact('project'));
    }

    /**
     * Salvează o factură pentru un proiect.
     */
    public function store(Project $project, Request $request): RedirectResponse
    {
        // aici pui validarea ta, de ex:
        $data = $request->validate([
            'file_name'      => ['required', 'string', 'max:255'],
            'drive_file_id'  => ['required', 'string', 'max:255'],
            'amount'         => ['nullable', 'numeric'],
            'currency'       => ['nullable', 'string', 'max:8'],
            'category'       => ['nullable', 'string', 'max:64'],
            'status'         => ['nullable', 'string', 'max:32'],
        ]);

        $invoice = new Invoice();
        $invoice->project_id = $project->id;
        $invoice->file_name = $data['file_name'];
        $invoice->drive_file_id = $data['drive_file_id'];
        $invoice->amount = $data['amount'] ?? null;
        $invoice->currency = $data['currency'] ?? 'CHF';
        $invoice->category = $data['category'] ?? null;
        $invoice->status = $data['status'] ?? 'imported';

        $invoice->save();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Factura a fost creată cu succes.');
    }

    /**
     * Șterge o factură pentru un proiect.
     *
     * Route: DELETE /projects/{project}/invoices/{invoice}
     */
    public function destroy(Project $project, Invoice $invoice): RedirectResponse
    {
        // siguranță suplimentară: factura chiar aparține proiectului
        if ((int)$invoice->project_id !== (int)$project->id) {
            abort(404);
        }

        $invoice->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Factura a fost ștearsă cu succes.');
    }
}
