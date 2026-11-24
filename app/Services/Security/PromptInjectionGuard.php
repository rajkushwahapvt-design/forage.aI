<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Log;

class PromptInjectionGuard
{
    /**
     * Handle edge case: "Instructions hidden in uploaded files" (Indirect Prompt Injection)
     * Sanitizes untrusted user text, strips injection vectors, and wraps content safely in XML delimiters.
     */
    public function sanitizeAndWrap(string $untrustedText, string $sourceLabel = 'uploaded_content'): array
    {
        $originalLength = strlen($untrustedText);
        $injectionDetected = false;
        $matchedPatterns = [];

        // Check for common prompt injection and override attacks
        $attackPatterns = [
            '/(?:ignore|disregard|override|forget)\s+(?:all\s+)?(?:previous|system|above)\s+(?:instructions|prompts|rules)/i',
            '/(?:you\s+are\s+now|act\s+as|pretend\s+to\s+be)\s+(?:DAN|developer\s+mode|unrestricted|jailbroken)/i',
            '/(?:output|print|reveal|show|display)\s+(?:the\s+)?(?:system\s+prompt|developer\s+instructions|api\s+key)/i',
            '/###\s*system\s*instruction/i',
            '/\[INST\][\s\S]*?\[\/INST\]/i',
            '/<system>[\s\S]*?<\/system>/i',
            '/<im_start>[\s\S]*?<im_end>/i',
        ];

        foreach ($attackPatterns as $pattern) {
            if (preg_match($pattern, $untrustedText, $matches)) {
                $injectionDetected = true;
                $matchedPatterns[] = $matches[0];
                Log::warning("Potential Prompt Injection detected in {$sourceLabel}: " . $matches[0]);
                // Redact malicious directive
                $untrustedText = preg_replace($pattern, '[REDACTED_UNAUTHORIZED_DIRECTIVE]', $untrustedText);
            }
        }

        // Limit maximum character length to prevent context flooding / denial of service
        $maxChars = config('ai.guardrails.max_untrusted_chars', 30000);
        if (strlen($untrustedText) > $maxChars) {
            $untrustedText = substr($untrustedText, 0, $maxChars) . "\n...[TRUNCATED FOR LENGTH]";
        }

        // Strip dangerous control chars
        $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $untrustedText);

        // Safely wrap inside XML delimiters for Gemini
        $safeXmlWrapped = "<user_provided_data source=\"{$sourceLabel}\" is_untrusted=\"true\">\n"
            . htmlspecialchars($sanitized, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . "\n</user_provided_data>";

        return [
            'is_safe' => !$injectionDetected,
            'injection_detected' => $injectionDetected,
            'matched_patterns' => $matchedPatterns,
            'sanitized_text' => $sanitized,
            'wrapped_content' => $safeXmlWrapped,
        ];
    }
}
