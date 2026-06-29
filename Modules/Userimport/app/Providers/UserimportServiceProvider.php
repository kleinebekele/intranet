<?php

namespace Modules\Userimport\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Userimport\Console\UserImportCommand;
use Nwidart\Modules\Support\ModuleServiceProvider;

class UserimportServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Userimport';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'userimport';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        UserImportCommand::class,
    ];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules: täglicher User-Import.
     */
    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command('userimport:run')
            ->dailyAt(config('userimport.schedule_time', '03:00'))
            ->withoutOverlapping();
    }
}
