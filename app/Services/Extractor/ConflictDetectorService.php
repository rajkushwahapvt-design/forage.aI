<?php

namespace App\Services\Extractor;

use App\Models\Business;
use App\Models\DataConflict;
use Illuminate\Support\Facades\Log;

class ConflictDetectorService
{
    /**
     * Handle edge case: "Sources that disagree (e.g. two phone numbers or two different addresses)"
     * Compares extracted data from multiple sources and records conflicts for user review.
     */
    public function detectAndRecordConflicts(Business $business, array $sourceAData, string $sourceAName, array $sourceBData, string $sourceBName): array
    {
        $conflictsFound = [];

        // Compare Phone Numbers
        $phonesA = $this->extractPhones($sourceAData);
        $phonesB = $this->extractPhones($sourceBData);

        if (!empty($phonesA) && !empty($phonesB)) {
            $normalizedA = $this->normalizePhone($phonesA[0]);
            $normalizedB = $this->normalizePhone($phonesB[0]);

            if ($normalizedA !== $normalizedB) {
                $conflictsFound[] = [
                    'field' => 'phone',
                    'source_a' => $sourceAName,
                    'value_a' => $phonesA[0],
                    'source_b' => $sourceBName,
                    'value_b' => $phonesB[0],
                ];
            }
        }

        // Compare Addresses
        $addrA = $sourceAData['address'] ?? ($sourceAData['full_address'] ?? null);
        $addrB = $sourceBData['address'] ?? ($sourceBData['full_address'] ?? null);

        if (!empty($addrA) && !empty($addrB)) {
            similar_text(strtolower(trim($addrA)), strtolower(trim($addrB)), $addrSim);
            if ($addrSim < 60) { // Significant divergence
                $conflictsFound[] = [
                    'field' => 'address',
                    'source_a' => $sourceAName,
                    'value_a' => $addrA,
                    'source_b' => $sourceBName,
                    'value_b' => $addrB,
                ];
            }
        }

        // Compare Business Hours if present
        $hoursA = $sourceAData['hours'] ?? null;
        $hoursB = $sourceBData['hours'] ?? null;

        if (!empty($hoursA) && !empty($hoursB) && is_string($hoursA) && is_string($hoursB)) {
            if (trim($hoursA) !== trim($hoursB)) {
                $conflictsFound[] = [
                    'field' => 'business_hours',
                    'source_a' => $sourceAName,
                    'value_a' => $hoursA,
                    'source_b' => $sourceBName,
                    'value_b' => $hoursB,
                ];
            }
        }

        // Persist conflicts into database
        foreach ($conflictsFound as $conflict) {
            DataConflict::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'field' => $conflict['field'],
                ],
                [
                    'source_a' => $conflict['source_a'],
                    'value_a' => $conflict['value_a'],
                    'source_b' => $conflict['source_b'],
                    'value_b' => $conflict['value_b'],
                    'status' => 'pending',
                ]
            );
        }

        if (!empty($conflictsFound)) {
            $business->update(['has_unresolved_conflicts' => true]);
        }

        return $conflictsFound;
    }

    protected function extractPhones(array $data): array
    {
        if (isset($data['phone'])) return [(string)$data['phone']];
        if (isset($data['phones']) && is_array($data['phones'])) return $data['phones'];
        return [];
    }

    protected function normalizePhone(string $phone): string
    {
        // Strip non-digits
        $digits = preg_replace('/\D/', '', $phone);
        // If has 91 country code prefix (12 digits), take last 10
        if (strlen($digits) > 10) {
            return substr($digits, -10);
        }
        return $digits;
    }
}
