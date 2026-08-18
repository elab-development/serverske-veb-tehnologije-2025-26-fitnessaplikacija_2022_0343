<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

// Tanak klijent za javni wger.de REST API (katalog vežbi).
// Odvojen u servis da kontroler ostane fokusiran na HTTP odgovore, ne na parsiranje eksternog API-ja.
class WgerService
{
    private string $base = 'https://wger.de/api/v2';

    /**
     * Povlači jednu stranicu vežbi sa wger.de i vraća ih normalizovane za naš exercises model.
     */
    public function fetchExercisePage(int $limit = 20, int $offset = 0): array
    {
        $res = Http::timeout(10)->get("{$this->base}/exerciseinfo/", [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $res->throw();
        $data = $res->json();

        $items = collect($data['results'] ?? [])->map(function (array $e) {
            $en = collect($e['translations'] ?? [])->firstWhere('language', 2);

            return [
                'wger_id' => $e['id'],
                'name' => trim($en['name'] ?? ''),
                'category' => $e['category']['name'] ?? null,
                'muscles' => collect($e['muscles'] ?? [])->pluck('name_en')->filter()->values()->all(),
                'equipment' => collect($e['equipment'] ?? [])->pluck('name')->values()->all(),
            ];
        })->filter(fn ($e) => $e['name'] !== '')->values()->all();

        return [
            'items' => $items,
            'count' => $data['count'] ?? 0,
            'hasMore' => ! is_null($data['next'] ?? null),
        ];
    }
}
