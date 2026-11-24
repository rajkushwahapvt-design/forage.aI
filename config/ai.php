<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Gemini AI Configuration
    |--------------------------------------------------------------------------
    | Supports Gemini 1.5 Flash (default, high speed, large context) and Pro.
    */
    'api_key' => env('GEMINI_API_KEY', ''),
    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    'fallback_model' => env('GEMINI_FALLBACK_MODEL', 'gemini-1.5-pro'),
    'api_endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/',
    'timeout' => env('AI_TIMEOUT_SECONDS', 30),
    'max_retries' => env('AI_MAX_RETRIES', 3),

    /*
    |--------------------------------------------------------------------------
    | Prompt Injection & Anti-Scam Guardrails
    |--------------------------------------------------------------------------
    */
    'guardrails' => [
        'forbidden_keywords' => [
            'ignore all previous instructions',
            'ignore previous instructions',
            'system prompt override',
            'disregard above instructions',
            'print system instructions',
            'you are now DAN',
            'bypass restrictions',
        ],
        'max_untrusted_chars' => 30000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Scraper Configuration
    |--------------------------------------------------------------------------
    */
    'scraper' => [
        'user_agent' => env('SCRAPER_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'),
        'timeout' => env('SCRAPER_TIMEOUT_SECONDS', 10),
        'max_file_size_mb' => 20,
    ],
];
