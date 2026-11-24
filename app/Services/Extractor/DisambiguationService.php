<?php

namespace App\Services\Extractor;

class DisambiguationService
{
    /**
     * Handle edge case: "Several businesses with the same name"
     * Compares provided name & city with search results / Google places candidates.
     * Returns candidates with confidence rating and flags ambiguity.
     */
    public function disambiguate(string $businessName, ?string $city, array $foundCandidates = []): array
    {
        if (empty($foundCandidates)) {
            return [
                'has_multiple_matches' => false,
                'confirmed_candidate' => [
                    'name' => $businessName,
                    'city' => $city,
                    'is_exact' => true,
                ],
                'candidates' => [],
            ];
        }

        $normalizedCity = $city ? strtolower(trim($city)) : '';
        $matches = [];

        foreach ($foundCandidates as $candidate) {
            $candidateCity = strtolower(trim($candidate['city'] ?? ''));
            $score = 0;

            // Name similarity
            similar_text(strtolower($businessName), strtolower($candidate['name'] ?? ''), $namePercent);
            if ($namePercent > 70) $score += 50;

            // City match
            if (!empty($normalizedCity) && !empty($candidateCity)) {
                if (str_contains($candidateCity, $normalizedCity) || str_contains($normalizedCity, $candidateCity)) {
                    $score += 50;
                }
            }

            $candidate['match_confidence'] = min(100, $score);
            $matches[] = $candidate;
        }

        // Sort by confidence descending
        usort($matches, fn($a, $b) => $b['match_confidence'] <=> $a['match_confidence']);

        // Check if top 2 candidates have close scores (genuine ambiguity)
        $isAmbiguous = count($matches) > 1 && ($matches[0]['match_confidence'] - $matches[1]['match_confidence'] < 20);

        return [
            'has_multiple_matches' => count($matches) > 1,
            'is_ambiguous' => $isAmbiguous,
            'top_match' => $matches[0] ?? null,
            'candidates' => $matches,
            'prompt_user_to_confirm' => $isAmbiguous,
        ];
    }
}
