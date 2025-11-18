<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\Request;

class ProjectMilestoneController extends Controller
{
    public function create(Project $project)
    {
        return view('project_milestones.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'type'         => 'required|in:pm,installation',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'planned_date' => 'nullable|date',
            'actual_date'  => 'nullable|date',
            'status'       => 'required|in:not_started,in_progress,done',
            'sort_order'   => 'nullable|integer|min:0',
        ]);

        $validated['project_id'] = $project->id;

        ProjectMilestone::create($validated);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Milestone adăugat cu succes.');
    }

    public function edit(Project $project, ProjectMilestone $milestone)
    {
        return view('project_milestones.edit', compact('project', 'milestone'));
    }

    public function update(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $validated = $request->validate([
            'type'         => 'required|in:pm,installation',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'planned_date' => 'nullable|date',
            'actual_date'  => 'nullable|date',
            'status'       => 'required|in:not_started,in_progress,done',
            'sort_order'   => 'nullable|integer|min:0',
        ]);

        $milestone->update($validated);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Milestone actualizat.');
    }

    public function destroy(Project $project, ProjectMilestone $milestone)
    {
        $milestone->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Milestone șters.');
    }
    
    public function show(Project $project)
{   
    $project->load(['costItems', 'workerLogs', 'milestones', 'risks']);

    return view('projects.show', compact('project'));
}
/**
     * Recalculează și salvează procentul proiectului.
     */
    private function updateProjectProgress(Project $project)
    {
        // Reîmprospătăm relația milestones pentru a lua datele noi
        $project->load('milestones');

        // Folosim accessor-ul creat în model
        $newPercent = $project->calculated_percent_complete;

        // Salvăm direct în tabela projects
        $project->update(['percent_complete' => $newPercent]);
    }
}


