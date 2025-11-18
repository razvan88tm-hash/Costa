<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCostItem;
use Illuminate\Http\Request;

class ProjectCostItemController extends Controller
{
    /**
     * Afișează formularul de creare a unui cost pentru un proiect.
     */
    public function create(Project $project)
    {
        return view('project_cost_items.create', compact('project'));
    }

    /**
     * Salvează un cost nou în baza de date.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'category'             => 'nullable|string|max:255',
            'line_item'            => 'nullable|string|max:255',
            'planned_budget'       => 'nullable|numeric|min:0',
            'committed_po'         => 'nullable|numeric|min:0',
            'actual_cost'          => 'nullable|numeric|min:0',
            'forecast_to_complete' => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string',
        ]);

        $validated['project_id'] = $project->id;

        if (isset($validated['planned_budget'], $validated['actual_cost'])) {
            $validated['variance_act_plan'] = $validated['actual_cost'] - $validated['planned_budget'];

            if ($validated['planned_budget'] > 0) {
                $validated['percent_variance'] = $validated['variance_act_plan'] / $validated['planned_budget'];
            }
        }

        ProjectCostItem::create($validated);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Cost material adăugat cu succes.');
    }

    /**
     * Formular de editare cost.
     */
    public function edit(Project $project, ProjectCostItem $costItem)
    {
        return view('project_cost_items.edit', compact('project', 'costItem'));
    }

    /**
     * Actualizare cost.
     */
    public function update(Request $request, Project $project, ProjectCostItem $costItem)
    {
        $validated = $request->validate([
            'category'             => 'nullable|string|max:255',
            'line_item'            => 'nullable|string|max:255',
            'planned_budget'       => 'nullable|numeric|min:0',
            'committed_po'         => 'nullable|numeric|min:0',
            'actual_cost'          => 'nullable|numeric|min:0',
            'forecast_to_complete' => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string',
        ]);

        if (isset($validated['planned_budget'], $validated['actual_cost'])) {
            $validated['variance_act_plan'] = $validated['actual_cost'] - $validated['planned_budget'];

            if ($validated['planned_budget'] > 0) {
                $validated['percent_variance'] = $validated['variance_act_plan'] / $validated['planned_budget'];
            } else {
                $validated['percent_variance'] = null;
            }
        }

        $costItem->update($validated);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Cost material actualizat cu succes.');
    }

    /**
     * Ștergere cost.
     */
    public function destroy(Project $project, ProjectCostItem $costItem)
    {
        $costItem->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Cost material șters.');
    }
}
