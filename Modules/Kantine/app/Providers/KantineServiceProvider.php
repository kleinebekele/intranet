<?php

namespace Modules\Kantine\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Kantine\Console\HolidayImportCommand;
use Nwidart\Modules\Support\ModuleServiceProvider;

class KantineServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Kantine';

    protected string $nameLower = 'kantine';

    /** @var string[] */
    protected array $commands = [
        HolidayImportCommand::class,
    ];

    /** @var string[] */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Feiertage-Import einmal im Monat automatisch ausführen.
     */
    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command('kantine:import-holidays')
            ->monthlyOn(1, '04:00')
            ->withoutOverlapping();
    }
}
