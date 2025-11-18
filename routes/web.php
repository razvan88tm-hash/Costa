<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectCostItemController;
use App\Http\Controllers\ProjectWorkerLogController;
use App\Http\Controllers\ProjectMilestoneController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DriveSyncController;
use App\Http\Controllers\DocAiDebugController;
use App\Http\Controllers\DocumentAiDebugController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ====== Home ======

Route::get('/', function () {
    return redirect()->route('projects.index');
})->name('home');

// ====== Drive Sync & DocAI debug ======

Route::get('/drive/sync', [DriveSyncController::class, 'sync'])
    ->name('drive.sync');

// doar dacă ai metoda logs() în DriveSyncController
Route::get('/sync/logs', [DriveSyncController::class, 'logs'])
    ->name('sync.logs');

// DocAI debug simplu
Route::get('/docai/debug', [DocAiDebugController::class, 'index'])
    ->name('docai.index');

Route::get('/docai/debug/{file}', [DocAiDebugController::class, 'show'])
    ->name('docai.show');

// Document AI debug (pagina de debug pentru o factură)
Route::get('/document-ai/debug', [DocumentAiDebugController::class, 'index'])
    ->name('document-ai.debug');

Route::get('/document-ai/debug/{invoice}', [DocumentAiDebugController::class, 'show'])
    ->name('documentai.debug');

// ====== Settings ======

Route::get('/settings', [SettingController::class, 'edit'])
    ->name('settings.edit');

Route::put('/settings', [SettingController::class, 'update'])
    ->name('settings.update');

// ====== Invoices (nested în projects) ======

// formular de creare factură pt un proiect
Route::get('/projects/{project}/invoices/create', [InvoiceController::class, 'create'])
    ->name('projects.invoices.create');

// salvare factură pt un proiect
Route::post('/projects/{project}/invoices', [InvoiceController::class, 'store'])
    ->name('projects.invoices.store');

// ștergere factură pt un proiect
Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])
    ->name('invoices.destroy');


// ====== Projects (CRUD principal) ======

Route::resource('projects', ProjectController::class);

// ====== Costuri materiale ======

Route::get('projects/{project}/cost-items/create', [ProjectCostItemController::class, 'create'])
    ->name('project-cost-items.create');

Route::post('projects/{project}/cost-items', [ProjectCostItemController::class, 'store'])
    ->name('project-cost-items.store');

Route::get('projects/{project}/cost-items/{costItem}/edit', [ProjectCostItemController::class, 'edit'])
    ->name('project-cost-items.edit');

Route::put('projects/{project}/cost-items/{costItem}', [ProjectCostItemController::class, 'update'])
    ->name('project-cost-items.update');

Route::delete('projects/{project}/cost-items/{costItem}', [ProjectCostItemController::class, 'destroy'])
    ->name('project-cost-items.destroy');

// ====== Costuri muncitori ======

Route::get('projects/{project}/worker-logs/create', [ProjectWorkerLogController::class, 'create'])
    ->name('project-worker-logs.create');

Route::post('projects/{project}/worker-logs', [ProjectWorkerLogController::class, 'store'])
    ->name('project-worker-logs.store');

Route::get('projects/{project}/worker-logs/export', [ProjectWorkerLogController::class, 'export'])
    ->name('project-worker-logs.export');

Route::get('projects/{project}/worker-logs/{workerLog}/edit', [ProjectWorkerLogController::class, 'edit'])->name('project-worker-logs.edit');

Route::put('projects/{project}/worker-logs/{workerLog}', [ProjectWorkerLogController::class, 'update'])->name('project-worker-logs.update');

Route::delete('projects/{project}/worker-logs/{workerLog}', [ProjectWorkerLogController::class, 'destroy'])->name('project-worker-logs.destroy');

// ====== Milestones ======

Route::get('projects/{project}/milestones/create', [ProjectMilestoneController::class, 'create'])
    ->name('project-milestones.create');

Route::post('projects/{project}/milestones', [ProjectMilestoneController::class, 'store'])
    ->name('project-milestones.store');

Route::get('projects/{project}/milestones/{milestone}/edit', [ProjectMilestoneController::class, 'edit'])
    ->name('project-milestones.edit');

Route::put('projects/{project}/milestones/{milestone}', [ProjectMilestoneController::class, 'update'])
    ->name('project-milestones.update');

Route::delete('projects/{project}/milestones/{milestone}', [ProjectMilestoneController::class, 'destroy'])
    ->name('project-milestones.destroy');
