<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DaPriceSyncService
{
    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent';

    /**
     * Maximum characters of HTML text content to send to the AI.
     */
    private const MAX_CONTENT_CHARS = 30000;

    /**
     * Maximum PDF file size to send inline to Gemini (20 MB).
     */
    private const MAX_PDF_BYTES = 20 * 1024 * 1024;

    private const EXTRACTION_PROMPT = <<<'PROMPT'
You are a data extraction assistant for a Philippine agricultural price monitoring system.

Your task is to extract all **prices** for agricultural crops/commodities from this document. The document might list them as prevailing prices, market prices, or suggested retail prices. Extract them all.

Return ONLY a JSON object with a single key "prices" containing an array of items. Each item must have exactly these fields:
- "crop": the crop or commodity name in English (e.g. "Rice", "Corn", "Mung Bean", "Onion", "Tomato", "Garlic")
- "specification": the variety or grade in lowercase (e.g. "well milled", "yellow corn", "local", "imported") — use "regular" if not specified
- "max_price": the price as a plain number in Philippine Pesos per kilogram (per kg)

Rules:
- Only include items with an explicit numeric price — skip rows with no price or "n/a"
- If a price range is given (e.g. "45-50"), use the HIGHER value
- If prices are per piece or per bundle (not per kg), skip them
- Normalize crop names to English common names
- Return {"prices": []} if truly no prices are found
PROMPT;

    /**
     * Fetch a DA price monitoring URL (HTML page or PDF) and use Gemini AI to extract ceiling prices.
     *
     * @return array{success: bool, prices: list<array{crop: string, specification: string, max_price: float}>, source_url: string, source_type: string, error?: string}
     */
    public function syncFromUrl(string $url): array
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            return $this->failure($url, 'unknown', 'Gemini API key is not configured.');
        }

        // Fetch the URL
        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Pricely/1.0)'])
                ->get($url);

            if (! $response->successful()) {
                return $this->failure($url, 'unknown', "Failed to fetch the URL (HTTP {$response->status()}). Make sure it is publicly accessible.");
            }

            $contentType = strtolower($response->header('Content-Type') ?? '');
            $rawBody = $response->body();
        } catch (\Exception $e) {
            Log::error('DaPriceSyncService: URL fetch failed', ['url' => $url, 'error' => $e->getMessage()]);

            return $this->failure($url, 'unknown', 'Could not reach the URL: '.$e->getMessage());
        }

        // Detect PDF by Content-Type or file extension
        $isPdf = str_contains($contentType, 'pdf')
            || str_ends_with(strtolower(parse_url($url, PHP_URL_PATH) ?? ''), '.pdf');

        if ($isPdf) {
            return $this->syncFromPdf($rawBody, $url, $apiKey);
        }

        return $this->syncFromHtml($rawBody, $url, $apiKey);
    }

    /**
     * Send a PDF directly to Gemini using its native multimodal vision capability.
     * Works for both text-based and image/infographic PDFs.
     *
     * @return array{success: bool, prices: list<array{crop: string, specification: string, max_price: float}>, source_url: string, source_type: string, error?: string}
     */
    private function syncFromPdf(string $pdfBinary, string $url, string $apiKey): array
    {
        if (strlen($pdfBinary) > self::MAX_PDF_BYTES) {
            return $this->failure($url, 'pdf', 'The PDF file is too large (over 20 MB). Please use a smaller file or a direct webpage URL.');
        }

        $base64Pdf = base64_encode($pdfBinary);

        try {
            $prices = $this->callGeminiWithParts($apiKey, [
                [
                    'inlineData' => [
                        'mimeType' => 'application/pdf',
                        'data' => $base64Pdf,
                    ],
                ],
                ['text' => self::EXTRACTION_PROMPT],
            ], $url);
        } catch (\Exception $e) {
            return $this->failure($url, 'pdf', $e->getMessage());
        }

        if ($prices === null) {
            return $this->failure($url, 'pdf', 'AI could not extract price data from the PDF. It may be a purely decorative infographic without structured price data.');
        }

        if (empty($prices)) {
            return $this->failure($url, 'pdf', 'No price data was found in this PDF. Try a different document or a webpage URL.');
        }

        return ['success' => true, 'prices' => $prices, 'source_url' => $url, 'source_type' => 'pdf'];
    }

    /**
     * Extract text from an HTML page and send it to Gemini as plain text.
     *
     * @return array{success: bool, prices: list<array{crop: string, specification: string, max_price: float}>, source_url: string, source_type: string, error?: string}
     */
    private function syncFromHtml(string $html, string $url, string $apiKey): array
    {
        $text = $this->extractTextFromHtml($html);

        if (! $text) {
            return $this->failure($url, 'html', 'Could not extract any text content from the page.');
        }

        if (strlen($text) > self::MAX_CONTENT_CHARS) {
            $text = substr($text, 0, self::MAX_CONTENT_CHARS).'... [content truncated]';
        }

        $fullPrompt = self::EXTRACTION_PROMPT."\n\nPage content from {$url}:\n\n".$text;

        try {
            $prices = $this->callGeminiWithParts($apiKey, [
                ['text' => $fullPrompt],
            ], $url);
        } catch (\Exception $e) {
            return $this->failure($url, 'html', $e->getMessage());
        }

        if ($prices === null) {
            return $this->failure($url, 'html', 'AI could not extract price data from the page. The page may not contain price information in a recognizable format.');
        }

        if (empty($prices)) {
            return $this->failure($url, 'html', 'No price data was found on that page. Try a more specific DA price bulletin URL.');
        }

        return ['success' => true, 'prices' => $prices, 'source_url' => $url, 'source_type' => 'html'];
    }

    /**
     * Call the Gemini API with an arbitrary parts array and return parsed price rows.
     *
     * @param  list<array<string, mixed>>  $parts
     * @return list<array{crop: string, specification: string, max_price: float}>|null
     * @throws \Exception
     */
    private function callGeminiWithParts(string $apiKey, array $parts, string $sourceUrl): ?array
    {
        $response = Http::timeout(60)
            ->post(self::GEMINI_API_URL.'?key='.$apiKey, [
                'contents' => [['parts' => $parts]],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 2048,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if (! $response->successful()) {
            $status = $response->status();
            Log::error('DaPriceSyncService: Gemini API error', [
                'url' => $sourceUrl,
                'status' => $status,
                'body' => $response->body(),
            ]);

            if ($status === 503) {
                throw new \Exception('The Gemini AI API is currently overloaded and experiencing high demand. Please wait a few minutes and try again.');
            }

            throw new \Exception("Gemini API error (Status $status). Please check your API key and quotas.");
        }

        $aiText = $response->json('candidates.0.content.parts.0.text', '');

        // Strip markdown code fences if the model wraps the JSON
        $aiText = trim(preg_replace('/^```(?:json)?\s*/i', '', $aiText) ?? '');
        $aiText = trim(preg_replace('/\s*```$/i', '', $aiText) ?? '');

        $data = json_decode($aiText, true);

        // Handle both {"prices": [...]} and [...] just in case
        $rows = isset($data['prices']) && is_array($data['prices']) ? $data['prices'] : (is_array($data) ? $data : null);

        if (! is_array($rows)) {
            Log::warning('DaPriceSyncService: AI returned non-array response', [
                'url' => $sourceUrl,
                'ai_text' => substr($aiText, 0, 500),
            ]);

            return null;
        }

        $prices = [];
        foreach ($rows as $row) {
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
    }

    /**
     * Strip HTML and extract meaningful plain text.
     */
    private function extractTextFromHtml(string $html): string
    {
        $html = preg_replace('/<(script|style|nav|header|footer|noscript)[^>]*>.*?<\/\1>/si', '', $html);
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s{3,}/', "\n\n", $text);

        return trim($text ?? '');
    }

    /**
     * Build a standard failure response.
     *
     * @return array{success: bool, prices: list<empty>, source_url: string, source_type: string, error: string}
     */
    private function failure(string $url, string $type, string $error): array
    {
        return [
            'success' => false,
            'prices' => [],
            'source_url' => $url,
            'source_type' => $type,
            'error' => $error,
        ];
    }
}
