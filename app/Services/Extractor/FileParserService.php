<?php

namespace App\Services\Extractor;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileParserService
{
    /**
     * Parse uploaded file (PDF, CSV, TXT) and extract text & facts.
     */
    public function parseFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        if ($extension === 'pdf' || str_contains($mime, 'pdf')) {
            return $this->parsePdf($file);
        }

        if (in_array($extension, ['csv', 'txt', 'tsv'])) {
            return $this->parseCsvOrText($file);
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
            return [
                'type' => 'image',
                'original_name' => $file->getClientOriginalName(),
                'text_content' => '',
                'parsed_facts' => [
                    'image_type' => 'showcase_or_logo',
                ],
            ];
        }

        return [
            'type' => 'unknown',
            'original_name' => $file->getClientOriginalName(),
            'text_content' => '',
            'parsed_facts' => [],
        ];
    }

    /**
     * PDF parser with fallback to native stream extraction.
     */
    protected function parsePdf(UploadedFile $file): array
    {
        $text = '';
        $path = $file->getRealPath();

        // Check if Smalot PDFParser is available
        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($path);
                $text = $pdf->getText();
            } catch (\Throwable $e) {
                Log::warning("Smalot PDF parse failed, trying stream: " . $e->getMessage());
            }
        }

        // Fallback: extract plain ASCII text streams from PDF
        if (empty(trim($text))) {
            $raw = file_get_contents($path);
            // Search for BT ... ET text blocks in PDF stream
            preg_match_all('/BT[\s\S]*?ET/', $raw, $matches);
            $extracted = '';
            foreach ($matches[0] as $block) {
                preg_match_all('/\((.*?)\)[\s]*T[jJ]/', $block, $tMatches);
                if (!empty($tMatches[1])) {
                    $extracted .= implode(' ', $tMatches[1]) . "\n";
                }
            }
            $text = trim($extracted);
            if (empty($text)) {
                // Last resort: extract printable ASCII strings
                preg_match_all('/[a-zA-Z0-9\s.,:\'\"\(\)\-\/\$\₹\%\&]{4,}/', $raw, $asciiMatches);
                $text = implode(' ', array_slice($asciiMatches[0], 0, 500));
            }
        }

        return [
            'type' => 'pdf',
            'original_name' => $file->getClientOriginalName(),
            'text_content' => substr($text, 0, 25000), // safety bounded
            'parsed_facts' => $this->extractFactsFromText($text),
        ];
    }

    /**
     * Parse CSV or formatted price lists.
     */
    protected function parseCsvOrText(UploadedFile $file): array
    {
        $content = file_get_contents($file->getRealPath());
        $lines = explode("\n", $content);
        $items = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Check if CSV format (comma, semicolon, or tab)
            $cols = str_getcsv($line);
            if (count($cols) >= 2) {
                $items[] = [
                    'name' => trim($cols[0]),
                    'price' => trim($cols[1]),
                    'description' => isset($cols[2]) ? trim($cols[2]) : null,
                ];
            }
        }

        return [
            'type' => 'csv_pricelist',
            'original_name' => $file->getClientOriginalName(),
            'text_content' => substr($content, 0, 15000),
            'parsed_facts' => [
                'items_count' => count($items),
                'items' => array_slice($items, 0, 30),
                'raw_text_summary' => substr($content, 0, 3000),
            ],
        ];
    }

    /**
     * Extract key patterns (phones, prices, services) from raw text.
     */
    protected function extractFactsFromText(string $text): array
    {
        $facts = [
            'phones' => [],
            'emails' => [],
            'prices' => [],
        ];

        // Indian and international phones
        preg_match_all('/(?:\+91[\-\s]?)?[6-9]\d{9}|(?:\+?1[\-\s]?)?\(?\d{3}\)?[\-\s]?\d{3}[\-\s]?\d{4}/', $text, $pMatches);
        if (!empty($pMatches[0])) {
            $facts['phones'] = array_values(array_unique(array_slice($pMatches[0], 0, 5)));
        }

        // Emails
        preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}/', $text, $eMatches);
        if (!empty($eMatches[0])) {
            $facts['emails'] = array_values(array_unique(array_slice($eMatches[0], 0, 5)));
        }

        // Pricing patterns (₹ 500, Rs. 1200, $25, etc.)
        preg_match_all('/(?:Rs\.?|₹|\$)\s*[\d,]+(?:\.\d{2})?/', $text, $priceMatches);
        if (!empty($priceMatches[0])) {
            $facts['prices'] = array_values(array_unique(array_slice($priceMatches[0], 0, 15)));
        }

        return $facts;
    }
}
