<?php

namespace App\Services\AI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class GeminiClient
{
    protected Client $httpClient;
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('ai.api_key', env('GEMINI_API_KEY', ''));
        $this->model = config('ai.model', 'gemini-1.5-flash');
        $this->endpoint = config('ai.api_endpoint', 'https://generativelanguage.googleapis.com/v1beta/models/');
        $this->httpClient = new Client([
            'timeout' => config('ai.timeout', 30),
            'http_errors' => false,
            'verify' => false,
        ]);
    }

    /**
     * Check if API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_gemini_api_key_here';
    }

    /**
     * Generate structured JSON content using Gemini API with retry and fallback.
     */
    public function generateJson(string $prompt, string $systemInstruction, array $jsonSchema = []): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Gemini API key is not configured in .env (GEMINI_API_KEY).');
        }

        $url = "{$this->endpoint}{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'systemInstruction' => [
                'role' => 'system',
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0.2, // Low temperature for high factual adherence
                'topP' => 0.8,
            ]
        ];

        $maxRetries = config('ai.max_retries', 3);
        $attempts = 0;
        $delayMs = 1000;

        while ($attempts < $maxRetries) {
            $attempts++;
            try {
                $response = $this->httpClient->post($url, [
                    'json' => $payload,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ]
                ]);

                $statusCode = $response->getStatusCode();
                $body = (string) $response->getBody();

                if ($statusCode === 200) {
                    $decodedResponse = json_decode($body, true);
                    $text = $decodedResponse['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if ($text) {
                        $parsedData = json_decode($text, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($parsedData)) {
                            return $parsedData;
                        }
                    }
                    throw new \RuntimeException('Invalid JSON returned by Gemini: ' . substr($body, 0, 500));
                }

                // Handle 429 Rate Limit or 503 Server Unavailable with backoff
                if ($statusCode === 429 || $statusCode >= 500) {
                    Log::warning("Gemini API HTTP {$statusCode} on attempt {$attempts}. Retrying in {$delayMs}ms...");
                    usleep($delayMs * 1000);
                    $delayMs *= 2; // Exponential backoff
                    continue;
                }

                throw new \RuntimeException("Gemini API Error {$statusCode}: " . substr($body, 0, 500));
            } catch (RequestException $e) {
                Log::warning("Gemini API Request failed (attempt {$attempts}): " . $e->getMessage());
                if ($attempts >= $maxRetries) {
                    throw $e;
                }
                usleep($delayMs * 1000);
                $delayMs *= 2;
            }
        }

        throw new \RuntimeException('Exceeded maximum Gemini API retries.');
    }
}
