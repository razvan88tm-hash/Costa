<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Invoice;
use App\Models\Project;
use App\Services\GoogleDriveService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class DriveSyncController extends Controller
{
    private const FOLDER_INVOICES = '02_Facturi';
    private const FOLDER_LAYOUT   = '03_Layout';
    private const FOLDER_OFFERS   = '04_Oferte';

    private GoogleDriveService $drive;

    public function __construct(GoogleDriveService $drive)
    {
        $this->drive = $drive;
    }

    /**
     * /sync/drive – pornește sincronizarea proiectelor + facturilor.
     */
    public function sync(): RedirectResponse
    {
        try {
            if (!method_exists(AppSetting::class, 'getSingleton')) {
                throw new Exception('AppSetting::getSingleton() is not defined. Please implement this method in the AppSetting model.');
            }
            $settings = AppSetting::getSingleton();

            if (!$settings) {
                throw new Exception('App settings could not be loaded. Please check the AppSetting::getSingleton() implementation.');
            }

            $rootFolderId       = $settings->drive_projects_folder_id;
            $docAiProcessorName = $settings->doc_ai_processor_id; // projects/.../locations/eu/processors/...

            Log::info('DriveSync: start', [
                'rootFolderId'  => $rootFolderId,
                'processorName' => $docAiProcessorName,
            ]);

            if (empty($rootFolderId)) {
                throw new Exception('Root projects folder ID is not set in Settings.');
            }

            if (empty($docAiProcessorName)) {
                throw new Exception('Document AI processor ID is not set in Settings.');
            }

            $projectFolders = $this->drive->listFoldersIn($rootFolderId);

            $summary = [
                'newProjects'     => 0,
                'updatedProjects' => 0,
                'newInvoices'     => 0,
            ];

            if (!is_iterable($projectFolders)) {
                Log::warning('DriveSync: listFoldersIn did not return iterable', [
                    'rootFolderId' => $rootFolderId,
                    'projectFolders' => $projectFolders,
                ]);
                return redirect()
                    ->route('projects.index')
                    ->with('error', 'Eroare: Nu s-au putut obține folderele proiectelor din Google Drive.');
            }

            foreach ($projectFolders as $folder) {
                // Defensive: check if $folder is an object and has the required methods
                if (!is_object($folder) || !method_exists($folder, 'getName') || !method_exists($folder, 'getId')) {
                    Log::warning('DriveSync: skip folder (invalid object)', [
                        'folder' => $folder,
                    ]);
                    continue;
                }
            
                $folderName = $folder->getName(); // ex: "1313_Kleymann"
                $folderId   = $folder->getId();
            
                [$projectNumber, $clientName] = $this->parseFolderName($folderName);
            
                if (! $projectNumber || ! $clientName) {
                    Log::info('DriveSync: skip folder (invalid name)', [
                        'folder' => $folderName,
                    ]);
            
                    continue;
                }
            
                $project = $this->createOrUpdateProjectFromFolder(
                    $projectNumber,
                    $clientName,
                    $folderId,
                    $folderName,
                    $summary
                );
            
                // Sincronizăm facturile
                $summary['newInvoices'] += $this->syncInvoicesForProject(
                    $project,
                    $folderId,
                    $docAiProcessorName
                );
            }

            Log::info('DriveSync: finished', $summary);

            $msg = sprintf(
                'Sync OK — %d new, %d updated, %d invoices.',
                $summary['newProjects'],
                $summary['updatedProjects'],
                $summary['newInvoices'],
            );

            return redirect()
                ->route('projects.index')
                ->with('success', $msg);

        } catch (Exception $e) {
            Log::error('Drive sync fatal error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('projects.index')
                ->with('error', 'Eroare Sincronizare: ' . $e->getMessage());
        }
    }

    /**
     * Creează sau actualizează un proiect pe baza folderului din Drive.
     */
    private function createOrUpdateProjectFromFolder(
        string $projectNumber,
        string $clientName,
        string $folderId,
        string $folderName,
        array &$summary
    ): Project {
        /** @var Project $project */
        $project = Project::firstOrNew(['project_number' => $projectNumber]);

        if (! $project->exists) {
            // Proiect nou
            $project->client_name = $clientName;
            $project->name        = $folderName;
            $project->overall_rag = 'Green';
            $summary['newProjects']++;
        } else {
            // Proiect existent
            $summary['updatedProjects']++;
        }

        // Link folder principal
        $project->drive_folder_url = $this->buildDriveFolderUrl($folderId);

        // 03_Layout
        $layoutId = $this->drive->findSubfolderIdByName($folderId, self::FOLDER_LAYOUT);
        if ($layoutId) {
            $project->layout_file_url = $this->buildDriveFolderUrl($layoutId);
        }

        // 04_Oferte
        $offersId = $this->drive->findSubfolderIdByName($folderId, self::FOLDER_OFFERS);
        if ($offersId) {
            $project->offers_folder_url = $this->buildDriveFolderUrl($offersId);
        }

        // TODO: 01_Contract – logică viitoare DocAI pt contract

        $project->save();

        // Logging after save to ensure $project->id is available
        if ($project->wasRecentlyCreated) {
            Log::info('DriveSync: new project', [
                'project_id'     => $project->id,
                'project_number' => $projectNumber,
                'client'         => $clientName,
            ]);
        } else {
            Log::info('DriveSync: existing project', [
                'project_id'     => $project->id,
                'project_number' => $projectNumber,
            ]);
        }

        return $project;

        // 03_Layout
        $layoutId = $this->drive->findSubfolderIdByName($folderId, self::FOLDER_LAYOUT);
        if ($layoutId) {
            $project->layout_file_url = $this->buildDriveFolderUrl($layoutId);
        }

        // 04_Oferte
        $offersId = $this->drive->findSubfolderIdByName($folderId, self::FOLDER_OFFERS);
        if ($offersId) {
            $project->offers_folder_url = $this->buildDriveFolderUrl($offersId);
        }

        // TODO: 01_Contract – logică viitoare DocAI pt contract

        $project->save();

        return $project;
    }

    /**
     * Sincronizează facturile pentru un proiect (folderul 02_Facturi)
     * și întoarce câte facturi noi au fost create.
     */
    private function syncInvoicesForProject(
        Project $project,
        string $projectFolderGoogleId,
        string $docAiProcessorName
    ): int {
        // Găsim subfolderul 02_Facturi
        $invoicesFolderId = $this->drive->findSubfolderIdByName(
            $projectFolderGoogleId,
            self::FOLDER_INVOICES
        );

        if (! $invoicesFolderId) {
            // N-are facturi, ieșim frumos
            return 0;
        }

        // Luăm fișierele din 02_Facturi
        $files = $this->drive->getFilesInFolder($invoicesFolderId);

        if (!is_iterable($files)) {
            Log::warning('DriveSync: getFilesInFolder did not return iterable', [
                'project_id' => $project->id,
                'folder_id'  => $invoicesFolderId,
                'files'      => $files,
            ]);
            return 0;
        }

        Log::info('DriveSync: processing invoices', [
            'project_id' => $project->id,
            'count'      => is_iterable($files) ? \count(is_array($files) ? $files : iterator_to_array($files)) : 0,
        ]);

        $newInvoices = 0;

        foreach ($files as $file) {
            $fileId   = $file->getId();
            $fileName = $file->getName();

            // Sărim peste fișierele deja importate
            if (Invoice::where('drive_file_id', $fileId)->exists()) {
                continue;
            }

            $amount = null;

            // Încercăm să luăm suma din Document AI – dar nu blocăm flow-ul dacă pică
            try {
                $amount = $this->drive->processInvoiceWithDocAi(
                    $fileId,
                    $docAiProcessorName
                );
            } catch (\Throwable $e) {
                Log::error('Invoice AI error', [
                    'message'  => $e->getMessage(),
                    'fileId'   => $fileId,
                    'fileName' => $fileName,
                ]);
            }

            // IMPORTANT:
            //  - `status` trebuie să fie compatibil cu tipul coloanei din DB
            //  - asigură-te că există coloana `amount` + e în $fillable
            Invoice::create([
                'project_id'    => $project->id,
                'file_name'     => $fileName,
                'drive_file_id' => $fileId,
                'amount'        => $amount ?? 0,   // deocamdată 0 dacă AI pică
                'currency'      => 'CHF',
                'category'      => 'misc',
                'status'        => 'imported',     // sau 'draft' / alt status permis de DB
            ]);

            $newInvoices++;
        }

        return $newInvoices;
    }

    /**
     * Parsează un nume de folder gen "1313_Kleymann" -> ['1313', 'Kleymann'].
     */
    private function parseFolderName(string $name): array
    {
        $parts = explode('_', $name, 2);

        if (\count($parts) === 2 && \is_numeric($parts[0])) {
            return [$parts[0], $parts[1]];
        }

        return [null, null];
    }

    private function buildDriveFolderUrl(string $folderId): string
    {
        return 'https://drive.google.com/drive/folders/' . $folderId;
    }
}
