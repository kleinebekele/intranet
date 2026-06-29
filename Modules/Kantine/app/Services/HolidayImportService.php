<?php

namespace Modules\Kantine\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Kantine\Models\KantineHoliday;

class HolidayImportService
{
    /**
     * Importiert Feiertage und Schulferien für das angegebene Bundesland und die Jahre.
     *
     * @return array{feiertage: int, schulferien: int, errors: list<string>}
     */
    public function import(string $bundesland, ?array $years = null): array
    {
        $years ??= [(int) date('Y'), (int) date('Y') + 1];

        $feiertageCount = 0;
        $schulferienCount = 0;
        $errors = [];

        foreach ($years as $year) {
            KantineHoliday::where('bundesland', $bundesland)
                ->where('year', $year)
                ->delete();

            try {
                $feiertageCount += $this->importFeiertage($bundesland, $year);
            } catch (\Throwable $e) {
                Log::error("Kantine: Feiertage-Import fehlgeschlagen ({$bundesland}/{$year})", [
                    'error' => $e->getMessage(),
                ]);
                $errors[] = "Feiertage {$year}: {$e->getMessage()}";
            }

            try {
                $schulferienCount += $this->importSchulferien($bundesland, $year);
            } catch (\Throwable $e) {
                Log::error("Kantine: Schulferien-Import fehlgeschlagen ({$bundesland}/{$year})", [
                    'error' => $e->getMessage(),
                ]);
                $errors[] = "Schulferien {$year}: {$e->getMessage()}";
            }
        }

        return [
            'feiertage' => $feiertageCount,
            'schulferien' => $schulferienCount,
            'errors' => $errors,
        ];
    }

    /**
     * Importiert Feiertage von date.nager.at (kostenlose API).
     */
    protected function importFeiertage(string $bundesland, int $year): int
    {
        $response = Http::timeout(15)
            ->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/DE");

        if (! $response->successful()) {
            throw new \RuntimeException("API-Fehler: HTTP {$response->status()}");
        }

        $stateCode = "DE-{$bundesland}";
        $count = 0;

        foreach ($response->json() as $holiday) {
            $counties = $holiday['counties'] ?? null;

            if ($counties !== null && ! in_array($stateCode, $counties, true)) {
                continue;
            }

            KantineHoliday::create([
                'date' => $holiday['date'],
                'end_date' => null,
                'name' => $holiday['localName'],
                'type' => 'feiertag',
                'bundesland' => $bundesland,
                'year' => $year,
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Importiert Schulferien von ferien-api.de.
     */
    protected function importSchulferien(string $bundesland, int $year): int
    {
        $response = Http::timeout(15)
            ->get("https://ferien-api.de/api/v1/holidays/{$bundesland}/{$year}");

        if (! $response->successful()) {
            throw new \RuntimeException("API-Fehler: HTTP {$response->status()}");
        }

        $count = 0;

        foreach ($response->json() as $ferien) {
            $start = Carbon::parse($ferien['start'])->toDateString();
            $end = Carbon::parse($ferien['end'])->toDateString();

            KantineHoliday::create([
                'date' => $start,
                'end_date' => $end,
                'name' => $ferien['name'],
                'type' => 'schulferien',
                'bundesland' => $bundesland,
                'year' => $year,
            ]);
            $count++;
        }

        return $count;
    }
}
