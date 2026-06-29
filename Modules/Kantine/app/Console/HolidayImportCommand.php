<?php

namespace Modules\Kantine\Console;

use Illuminate\Console\Command;
use Modules\Kantine\Models\KantineSetting;
use Modules\Kantine\Services\HolidayImportService;

class HolidayImportCommand extends Command
{
    protected $signature = 'kantine:import-holidays
                            {--bundesland= : Bundesland-Kürzel (z. B. NW). Standard: aus den Einstellungen}';

    protected $description = 'Importiert Feiertage und Schulferien für das aktuelle und nächste Jahr';

    public function handle(HolidayImportService $service): int
    {
        $settings = KantineSetting::current();
        $bundesland = $this->option('bundesland') ?: $settings->bundesland;

        $this->info("Importiere Feiertage und Schulferien für {$bundesland} …");

        $result = $service->import($bundesland);

        $this->info("Feiertage importiert: {$result['feiertage']}");
        $this->info("Schulferien importiert: {$result['schulferien']}");

        foreach ($result['errors'] as $error) {
            $this->warn("Fehler: {$error}");
        }

        return empty($result['errors']) ? self::SUCCESS : self::FAILURE;
    }
}
