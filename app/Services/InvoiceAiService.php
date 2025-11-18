<?php

namespace App\Services;

use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\ProcessRequest;
use Google\Cloud\DocumentAI\V1\RawDocument;
use App\Models\AppSetting;

class InvoiceAiService
{
    public function processLocalPdf(string $absolutePathToPdf, string $mimeType = 'application/pdf'): array
    {
        // 1. Luăm setările globale (inclusiv processor_id)
        $settings = AppSetting::getSingleton();
        $processorId = $settings->doc_ai_processor_id;

        if (!$processorId) {
            throw new \RuntimeException('doc_ai_processor_id nu este setat în AppSetting.');
        }

        if (!file_exists($absolutePathToPdf)) {
            throw new \RuntimeException("Fișierul nu există: {$absolutePathToPdf}");
        }

        // 2. Citim conținutul fișierului
        $content = file_get_contents($absolutePathToPdf);

        // 3. Construim clientul (endpoint regional pentru EU)
        $client = new DocumentProcessorServiceClient([
            'apiEndpoint' => 'eu-documentai.googleapis.com',
        ]);

        // 4. Construim RawDocument
        $rawDocument = (new RawDocument())
            ->setContent($content)
            ->setMimeType($mimeType);

        // 5. Construim ProcessRequest
        $request = (new ProcessRequest())
            ->setName($processorId)      // projects/.../locations/.../processors/...
            ->setRawDocument($rawDocument);

        // 6. Apelăm API-ul
        $response = $client->processDocument($request);
        $document = $response->getDocument();

        // TODO: aici parsezi câmpurile relevante (total, TVA, etc.)
        // Deocamdată returnăm structurat câteva info brute
        return [
            'text' => $document->getText(),
            'entities' => collect((array) $document->getEntities())
                ->map(function ($entity) use ($document) {
                    $mentionText = '';
                    $textAnchor = $entity->getTextAnchor();
                    if ($textAnchor && $textAnchor->getTextSegments()) {
                        $mentionText = $this->getLayoutText($document, $textAnchor->getTextSegments());
                    }
                    return [
                        'type'  => $entity->getType(),
                        'mentionText' => $mentionText,
                        'confidence'  => $entity->getConfidence(),
                    ];
                })
                ->toArray(),
        ];
    }

    /**
     * Extrage textul pentru segmentele date (util pentru entități).
     */
    protected function getLayoutText($document, $textSegments): string
    {
        $text = $document->getText();
        $result = '';

        foreach ($textSegments as $segment) {
            $start = $segment->getStartIndex() ?? 0;
            $end   = $segment->getEndIndex();
            if ($end !== null) {
                $result .= mb_substr($text, $start, $end - $start);
            }
        }

        return trim($result);
    }
}
