<?php

namespace App\Services;

use Exception;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GoogleDriveService
{
    // REFACTOR: Constante pentru a evita "magic strings"
    private const MIME_TYPE_FOLDER = 'application/vnd.google-apps.folder';
    private const MIME_TYPE_PDF    = 'application/pdf';
    private const DOCAI_DEBUG_PATH = 'docai_debug';

    private Drive $drive;
    private GoogleClient $authClient;
    private HttpClient $httpClient;

    /**
     * REFACTOR: Constructorul ar trebui să primească dependențele (DI).
     * Clientul Guzzle este acum injectat.
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $credentialsPath  = storage_path('app/google/credentials.json');

        if (! file_exists($credentialsPath)) {
            throw new Exception("Google credentials file missing at: {$credentialsPath}");
        }

        $client = new GoogleClient();
        $client->setAuthConfig($credentialsPath);
        $client->setScopes([
            Drive::DRIVE,
            'https://www.googleapis.com/auth/cloud-platform',
        ]);

        $this->authClient = $client;
        $this->drive      = new Drive($client);
    }

    //================================================================
    // METODE GOOGLE DRIVE
    //================================================================

    /**
     * Listează subfolderele dintr-un folder părinte.
     */
    public function listFoldersIn(string $parentId): array
    {
        $query = sprintf(
            "'%s' in parents and mimeType = '%s' and trashed = false",
            $parentId,
            self::MIME_TYPE_FOLDER
        );

        $response = $this->drive->files->listFiles([
            'q'      => $query,
            'fields' => 'files(id,name)',
        ]);

        $files = $response->getFiles() ?? [];

        Log::info('Drive: listFoldersIn()', [
            'parent' => $parentId,
            'count'  => count($files),
        ]);

        return $files;
    }

    /**
     * Găsește ID-ul unui subfolder după nume.
     */
    public function findSubfolderIdByName(string $parentId, string $name): ?string
    {
        // Escape single quotes for Google Drive API query
        $safeName = str_replace("'", "\\'", $name);

        $query = sprintf(
            "'%s' in parents and name = '%s' and mimeType = '%s' and trashed = false",
            $parentId,
            $safeName,
            self::MIME_TYPE_FOLDER
        );

        $response = $this->drive->files->listFiles([
            'q'      => $query,
            'fields' => 'files(id)',
            'pageSize' => 1
        ]);

        $files = $response->getFiles() ?? [];

        if (empty($files)) {
            Log::info('Drive: findSubfolderIdByName() - Not found', [
                'parent'   => $parentId,
                'searched' => $name,
            ]);
            return null;
        }

        $id = $files[0]->getId();

        Log::info('Drive: findSubfolderIdByName() - Found', [
            'parent'   => $parentId,
            'searched' => $name,
            'found'    => $id,
        ]);

        return $id;
    }
    
    /**
     * Creează un folder nou dacă nu există deja.
     */
    public function createFolder(string $parentId, string $folderName): string
    {
        $existingId = $this->findSubfolderIdByName($parentId, $folderName);
        if ($existingId) {
            return $existingId;
        }

        $fileMetadata = new DriveFile([
            'name'     => $folderName,
            'mimeType' => self::MIME_TYPE_FOLDER,
            'parents'  => [$parentId]
        ]);

        $folder = $this->drive->files->create($fileMetadata, ['fields' => 'id']);
        $newId = $folder->getId();

        Log::info('Drive: createFolder() - Created new folder', [
            'parent'  => $parentId,
            'name'    => $folderName,
            'created' => $newId,
        ]);

        return $newId;
    }


    /**
     * Listează fișierele (ex. PDF) dintr-un folder.
     */
    public function getFilesInFolder(string $folderId, string $mimeType = self::MIME_TYPE_PDF): array
    {
        $query = sprintf(
            "'%s' in parents and mimeType = '%s' and trashed = false",
            $folderId,
            $mimeType
        );

        $response = $this->drive->files->listFiles([
            'q'      => $query,
            'fields' => 'files(id,name,mimeType)',
        ]);

        $files = $response->getFiles() ?? [];

        Log::info('Drive: getFilesInFolder()', [
            'folderId' => $folderId,
            'mimeType' => $mimeType,
            'count'    => count($files),
        ]);

        return $files;
    }

    /**
     * Descarcă conținutul unui fișier ca string de bytes.
     */
    public function downloadFileContent(string $fileId): string
    {
        $httpRequest = $this->drive->files->get($fileId, ['alt' => 'media']);
        // The Google API client returns a GuzzleHttp\Psr7\Stream or string depending on the transport
        if (method_exists($httpRequest, 'getBody')) {
            $body = $httpRequest->getBody();
            if ($body === null) {
                throw new Exception("File body is empty: {$fileId}");
            }
            return $body->getContents();
        } elseif (is_string($httpRequest)) {
            return $httpRequest;
        } elseif (is_resource($httpRequest)) {
            return stream_get_contents($httpRequest);
        } else {
            throw new Exception("Failed to download file: {$fileId}");
        }
    }


    //================================================================
    // METODE DOCUMENT AI
    //================================================================

    /**
     * Trimite factura la Document AI (REST) și întoarce datele parsate.
     *
     * @return array|null Un array structurat cu datele facturii sau null la eșec
     * @throws Throwable
     */
    public function processInvoiceWithDocAi(string $fileId, string $processorName): ?array
    {
        try {
            Log::info('DocAI: Processing started', [
                'fileId'    => $fileId,
                'processor' => $processorName,
            ]);

            // 1. Descarcă PDF-ul din Drive
            $pdfBytes = $this->downloadFileContent($fileId);
            $base64   = base64_encode($pdfBytes);

            // 2. Obține Access Token
            $accessToken = $this->getAccessToken();

            // 3. Rezolvă Endpoint-ul (eu/us/etc.)
            $host = $this->resolveDocAiEndpoint($processorName);
            $url  = sprintf('https://%s/v1/%s:process', $host, $processorName);

            // 4. Apel REST către Document AI
            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json'    => [
                    'rawDocument' => [
                        'content'  => $base64,
                        'mimeType' => self::MIME_TYPE_PDF,
                    ],
                ],
                'timeout' => 30,
            ]);

            $data = json_decode((string) $response->getBody(), true);

            // 5. Salvăm JSON de debug
            Storage::makeDirectory(self::DOCAI_DEBUG_PATH);
            Storage::put(
                self::DOCAI_DEBUG_PATH . '/' . $fileId . '.json',
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            // 6. Parsăm răspunsul
            if (empty($data['document']['entities'])) {
                Log::warning('DocAI: Response received, but no entities found.', [
                    'fileId' => $fileId
                ]);
                return null;
            }

            $parsedData = $this->parseInvoiceEntities($data['document']);

            Log::info('DocAI: Parsed invoice data successfully', [
                'fileId' => $fileId,
                'data'   => $parsedData
            ]);

            return $parsedData;
        
        } catch (Throwable $e) {
            Log::error('DocAI: Processing critical error', [
                'fileId' => $fileId,
                'proc'   => $processorName,
                'class'  => get_class($e),
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    //================================================================
    // HELPERS PRIVATI (DocAI Auth & Endpoint)
    //================================================================

    /**
     * Obține un access token OAuth2 folosind service account.
     */
    private function getAccessToken(): string
    {
        $tokenInfo = $this->authClient->fetchAccessTokenWithAssertion();

        if (isset($tokenInfo['error'])) {
            throw new Exception(
                'Cannot get access token: ' . ($tokenInfo['error_description'] ?? $tokenInfo['error'])
            );
        }

        return $tokenInfo['access_token'];
    }

    /**
     * Extrage regiunea (eu/us/...) din numele complet al procesorului.
     */
    private function resolveDocAiEndpoint(string $processorName): string
    {
        if (preg_match('~/locations/([^/]+)/~', $processorName, $m)) {
            $location = $m[1];
        } else {
            Log::warning('DocAI: Could not resolve location from processor. Defaulting to "us".', [
                'processor' => $processorName
            ]);
            $location = 'us';
        }

        return $location . '-documentai.googleapis.com';
    }


    //================================================================
    // HELPERS PRIVATI (DocAI Parser)
    //================================================================

    /**
     * Parsoare documentul AI și extrage date structurate.
     */
    private function parseInvoiceEntities(array $document): array
    {
        $entities = $document['entities'] ?? [];

        $parsedData = [
            'supplier_name'    => $this->findEntityProperty($entities, 'supplier_name'),
            'supplier_address' => $this->findEntityProperty($entities, 'supplier_address'),
            'invoice_id'       => $this->findEntityProperty($entities, 'invoice_id'),
            'invoice_date'     => $this->findEntityProperty($entities, 'invoice_date', 'normalizedValue.text'),
            'due_date'         => $this->findEntityProperty($entities, 'due_date', 'normalizedValue.text'),
            'total_amount'     => $this->parseAmountEntity($entities, 'total_amount'),
            'total_tax'        => $this->parseAmountEntity($entities, 'total_tax_amount'),
            'net_amount'       => $this->parseAmountEntity($entities, 'net_amount'),
            'line_items'       => [],
        ];

        // Extragem rândurile (line_items)
        foreach ($entities as $entity) {
            if (($entity['type'] ?? null) !== 'line_item') {
                continue;
            }

            // $props este lista de proprietăți DIN interiorul acestui 'line_item'
            $props = $entity['properties'] ?? [];

            $parsedData['line_items'][] = [
                'description' => $this->findEntityProperty($props, 'description'),
                'quantity'    => $this->parseAmountEntity($props, 'quantity'),
                'unit_price'  => $this->parseAmountEntity($props, 'unit_price'),
                'line_amount' => $this->parseAmountEntity($props, 'amount'),
            ];
        }

        return $parsedData;
    }

    /**
     * Helper pentru a găsi o proprietate dintr-o listă de entități.
     */
    private function findEntityProperty(array $entities, string $type, string $field = 'mentionText'): ?string
    {
        foreach ($entities as $entity) {
            if (($entity['type'] ?? null) !== $type) {
                continue;
            }

            if (strpos($field, '.') !== false) {
                $parts = explode('.', $field);
                $value = $entity;
                foreach ($parts as $part) {
                    if (!is_array($value) || !isset($value[$part])) {
                        $value = null;
                        break;
                    }
                    $value = $value[$part];
                }
                return is_string($value) ? $value : null;
            }

            $result = $entity[$field] ?? null;
            return is_string($result) ? $result : null;
        }
        return null;
    }

    /**
     * Helper avansat pentru a parsa o sumă (amount).
     */
    private function parseAmountEntity(array $entities, string $type): ?float
    {
        foreach ($entities as $entity) {
            if (($entity['type'] ?? null) !== $type) {
                continue;
            }

            if (isset($entity['normalizedValue']) && is_array($entity['normalizedValue'])) {
                if (isset($entity['normalizedValue']['units']) || isset($entity['normalizedValue']['nanos'])) {
                    $units = (float)($entity['normalizedValue']['units'] ?? 0);
                    $nanos = (float)($entity['normalizedValue']['nanos'] ?? 0);
                    return $units + ($nanos / 1_000_000_000);
                }
            }

            $text = $entity['mentionText'] ?? null;
            if ($text === null) {
                continue;
            }

            $text = preg_replace("/[^\d,.-]/", "", $text);
            if ($text === '') {
                continue;
            }

            $lastComma = strrpos($text, ',');
            $lastDot   = strrpos($text, '.');

            if ($lastComma !== false && $lastDot !== false) {
                if ($lastComma > $lastDot) {
                    $text = str_replace('.', '', $text);
                    $text = str_replace(',', '.', $text);
                } else {
                    $text = str_replace(',', '', $text);
                }
            } elseif ($lastComma !== false) {
                $text = str_replace(',', '.', $text);
            }

            return is_numeric($text) ? (float)$text : null;
        }
        return null;
    }
}