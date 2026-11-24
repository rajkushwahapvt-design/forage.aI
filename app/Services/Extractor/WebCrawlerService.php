<?php

namespace App\Services\Extractor;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class WebCrawlerService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => config('ai.scraper.timeout', 10),
            'connect_timeout' => 5,
            'headers' => [
                'User-Agent' => config('ai.scraper.user_agent'),
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
            ],
            'http_errors' => false,
            'verify' => false,
        ]);
    }

    /**
     * Crawl a website or social link with anti-scraping fallback.
     */
    public function crawlUrl(string $url): array
    {
        $domain = parse_url($url, PHP_URL_HOST) ?? '';

        // Check if known anti-scraping domain (e.g. instagram.com, facebook.com, linkedin.com)
        $isProtectedSocial = $this->isProtectedSocialSite($domain);

        try {
            $response = $this->client->get($url);
            $statusCode = $response->getStatusCode();
            $html = (string) $response->getBody();

            if ($statusCode === 403 || $statusCode === 429 || $statusCode === 999) {
                return [
                    'status' => 'blocked_anti_scraping',
                    'url' => $url,
                    'domain' => $domain,
                    'error' => "Source returned HTTP {$statusCode}. Platform employs anti-bot protection.",
                    'extracted_facts' => [
                        'social_platform' => $this->detectPlatform($domain),
                        'profile_url' => $url,
                        'manual_input_needed' => true,
                    ],
                ];
            }

            if ($statusCode >= 400) {
                return [
                    'status' => 'error',
                    'url' => $url,
                    'domain' => $domain,
                    'error' => "HTTP error {$statusCode}",
                    'extracted_facts' => [],
                ];
            }

            // Extract OpenGraph tags, title, meta description, emails, phones
            $extracted = $this->parseHtmlContent($html, $url);

            return [
                'status' => 'success',
                'url' => $url,
                'domain' => $domain,
                'raw_content' => substr(strip_tags($html), 0, 15000), // bounded
                'extracted_facts' => $extracted,
            ];
        } catch (\Throwable $e) {
            Log::warning("Crawling failed for {$url}: " . $e->getMessage());

            return [
                'status' => $isProtectedSocial ? 'blocked_anti_scraping' : 'timeout',
                'url' => $url,
                'domain' => $domain,
                'error' => $e->getMessage(),
                'extracted_facts' => [
                    'social_platform' => $this->detectPlatform($domain),
                    'profile_url' => $url,
                    'manual_input_needed' => true,
                ],
            ];
        }
    }

    /**
     * Parse HTML for meta tags, contact info, and business details.
     */
    protected function parseHtmlContent(string $html, string $sourceUrl): array
    {
        $facts = [
            'title' => '',
            'description' => '',
            'image' => '',
            'phones' => [],
            'emails' => [],
            'addresses' => [],
            'social_links' => [],
        ];

        // Match Title
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            $facts['title'] = trim(html_entity_decode($matches[1]));
        }

        // Match OpenGraph / Meta Description
        if (preg_match('/<meta[^>]*property=["\']og:description["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches)) {
            $facts['description'] = trim(html_entity_decode($matches[1]));
        } elseif (preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches)) {
            $facts['description'] = trim(html_entity_decode($matches[1]));
        }

        // Match OpenGraph Image
        if (preg_match('/<meta[^>]*property=["\']og:image["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches)) {
            $facts['image'] = trim($matches[1]);
        }

        // Extract phone numbers (Indian & International formats)
        preg_match_all('/(?:\+91[\-\s]?)?[6-9]\d{9}|(?:\+?1[\-\s]?)?\(?\d{3}\)?[\-\s]?\d{3}[\-\s]?\d{4}/', $html, $phoneMatches);
        if (!empty($phoneMatches[0])) {
            $facts['phones'] = array_values(array_unique(array_slice($phoneMatches[0], 0, 5)));
        }

        // Extract emails
        preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}/', $html, $emailMatches);
        if (!empty($emailMatches[0])) {
            $facts['emails'] = array_values(array_unique(array_slice($emailMatches[0], 0, 5)));
        }

        return $facts;
    }

    protected function isProtectedSocialSite(string $domain): bool
    {
        $protected = ['instagram.com', 'facebook.com', 'linkedin.com', 'twitter.com', 'x.com', 'tiktok.com'];
        foreach ($protected as $p) {
            if (str_contains(strtolower($domain), $p)) {
                return true;
            }
        }
        return false;
    }

    protected function detectPlatform(string $domain): string
    {
        if (str_contains($domain, 'instagram')) return 'Instagram';
        if (str_contains($domain, 'facebook')) return 'Facebook';
        if (str_contains($domain, 'linkedin')) return 'LinkedIn';
        if (str_contains($domain, 'twitter') || str_contains($domain, 'x.com')) return 'X / Twitter';
        if (str_contains($domain, 'google')) return 'Google Business';
        return 'Website';
    }
}
