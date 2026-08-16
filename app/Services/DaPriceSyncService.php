<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;

class DaPriceSyncService
{
    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    /**
     * Maximum characters of page content to send to the AI to stay within token limits.
     */
    private const MAX_CONTENT_CHARS = 30000;

    /**
     * Fetch a DA price monitoring URL (HTML page or PDF) and use Gemini AI to extract ceiling prices.
     *
     * @return array{success: bool, prices: list<array{crop: string, specification: string, max_price: float}>, source_url: string, source_type: string, error?: string}
     */
    public function syncFromUrl(string $url): array
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            return ['success' => false, 'prices' => [], 'source_url' => $url, 'source_type' => 'unknown', 'error' => 'Gemini API key is not configured.'];
        }

        // Step 1: Fetch the raw response
        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Pricely/1.0)'])
                ->get($url);

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'prices' => [],
                    'source_url' => $url,
                    'source_type' => 'unknown',
                    'error' => "Failed to fetch the URL (HTTP {$response->status()}). Make sure the URL is publicly accessible.",
                ];
            }

            $contentType = strtolower($response->header('Content-Type') ?? '');
            $rawBody = $response->body();
        } catch (\Exception $e) {
            Log::error('DaPriceSyncService: URL fetch failed', ['url' => $url, 'error' => $e->getMessage()]);

            return ['success' => false, 'prices' => [], 'source_url' => $url, 'source_type' => 'unknown', 'error' => 'Could not reach the URL: '.$e->getMessage()];
        }

        // Step 2: Detect content type and extract text accordingly
        $isPdf = str_contains($contentType, 'pdf')
            || str_ends_with(strtolower(parse_url($url, PHP_URL_PATH) ?? ''), '.pdf');

        if ($isPdf) {
            $result = $this->extractTextFromPdf($rawBody, $url);
        } else {
            $result = ['text' => $this->extractTextFromHtml($rawBody), 'type' => 'html'];
        }

        if (! $result['text']) {
            return [
                'success' => false,
                'prices' => [],
                'source_url' => $url,
                'source_type' => $result['type'],
                'error' => $isPdf
                    ? 'Could not extract text from the PDF. The file may be scanned/image-based or password-protected.'
                    : 'Could not extract any text content from the page.',
            ];
        }

        $text = $result['text'];

        // Step 3: Truncate to safe AI context size
        if (strlen($text) > self::MAX_CONTENT_CHARS) {
            $text = substr($text, 0, self::MAX_CONTENT_CHARS).'... [content truncated]';
        }

        // Step 4: Ask Gemini to extract ceiling prices
        $extractedPrices = $this->extractPricesWithGemini($text, $apiKey, $url);

        if ($extractedPrices === null) {
            return [
                'success' => false,
                'prices' => [],
                'source_url' => $url,
                'source_type' => $result['type'],
                'error' => 'AI could not extract price data from the page. The page may not contain price information in a recognizable format.',
            ];
        }

        return [
            'success' => true,
            'prices' => $extractedPrices,
            'source_url' => $url,
            'source_type' => $result['type'],
        ];
    }

    /**
     * Extract plain text from a PDF binary using smalot/pdfparser.
     *
     * @return array{text: string, type: string}
     */
    private function extractTextFromPdf(string $pdfBinary, string $url): array
    {
        try {
            $parser = new PdfParser;
            $pdf = $parser->parseContent($pdfBinary);
            $text = $pdf->getText();

            // Clean up excessive whitespace
            $text = preg_replace('/\s{3,}/', "\n\n", $text) ?? $text;
            $text = trim($text);

            return ['text' => $text, 'type' => 'pdf'];
        } catch (\Exception $e) {
            Log::error('DaPriceSyncService: PDF parse failed', ['url' => $url, 'error' => $e->getMessage()]);

            return ['text' => '', 'type' => 'pdf'];
        }
    }

    /**
     * Strip HTML and extract meaningful text content from a page.
     */
    private function extractTextFromHtml(string $html): string
    {
        // Remove script, style, nav, header, footer blocks
        $html = preg_replace('/<(script|style|nav|header|footer|noscript)[^>]*>.*?<\/\1>/si', '', $html);

        // Strip remaining tags
        $text = strip_tags($html);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Collapse excessive whitespace
        $text = preg_replace('/\s{3,}/', "\n\n", $text);
        $text = trim($text ?? '');

        return $text;
    }

    /**
     * Send extracted page text to Gemini AI and parse the ceiling price data.
     *
     * @return list<array{crop: string, specification: string, max_price: float}>|null
     */
    private function extractPricesWithGemini(string $pageText, string $apiKey, string $sourceUrl): ?array
    {
        $prompt = <<<PROMPT
You are a data extraction assistant for a Philippine agricultural price monitoring system.

Below is text extracted from a Department of Agriculture (DA) price monitoring document or page: {$sourceUrl}

Your task is to extract all **ceiling prices** (maximum allowed prices / suggested retail prices / price caps) for agricultural crops/commodities mentioned in the content.

Return ONLY a valid JSON array. Each element must have exactly these fields:
- "crop": the crop or commodity name in English (e.g. "Rice", "Corn", "Mung Bean", "Onion", "Tomato")
- "specification": the variety or grade (e.g. "well milled", "yellow corn", "local", "imported") — use "regular" if not specified
- "max_price": the ceiling/maximum price as a number (Philippine Pesos per kg)

Rules:
- Only include items with an explicit price value — skip any row that has no price or says "no data"
- If a price range is given (e.g. "45-50"), use the higher value
- Normalize crop names to English common names
- Return an empty array [] if no ceiling prices are found
- Return ONLY the JSON array, no explanation, no markdown, no code fences

Document content:
{$pageText}
PROMPT;

        try {
            $response = Http::timeout(45)
                ->post(self::GEMINI_API_URL.'?key='.$apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 2048,
                    ],
                ]);

            if (! $response->successful()) {
                Log::error('DaPriceSyncService: Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $aiText = $response->json('candidates.0.content.parts.0.text', '');

            // Strip markdown code fences if the model wraps the JSON
            $aiText = trim(preg_replace('/^```(?:json)?\s*/i', '', $aiText) ?? '');
            $aiText = trim(preg_replace('/\s*```$/i', '', $aiText) ?? '');

            $data = json_decode($aiText, true);

            if (! is_array($data)) {
                Log::warning('DaPriceSyncService: AI returned non-array JSON', ['ai_text' => $aiText]);

                return null;
            }

            // Validate and sanitize each row
            $prices = [];
            foreach ($data as $row) {
                if (! isset($row['crop'], $row['specification'], $row['max_price'])) {
                    continue;
                }

                $price = (float) $row['max_price'];
                if ($price <= 0) {
                    continue;
                }

                $prices[] = [
                    'crop' => trim((string) $row['crop']),
                    'specification' => strtolower(trim((string) $row['specification'])),
                    'max_price' => $price,
                ];
            }

            return $prices;
        } catch (\Exception $e) {
            Log::error('DaPriceSyncService: Gemini call exception', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
