<?php

namespace Modules\Userimport\Console;

use Illuminate\Console\Command;
use Modules\Userimport\Services\UserImporter;
use Throwable;

class UserImportCommand extends Command
{
    protected $signature = 'userimport:run {--file= : Pfad zur CSV-Datei (überschreibt die Konfiguration)}';

    protected $description = 'Importiert Benutzer aus einer CSV-Datei (neue anlegen, vorhandene ignorieren).';

    public function handle(UserImporter $importer): int
    {
        $path = $this->option('file') ?: config('userimport.path');

        $this->info("Starte User-Import aus: {$path}");

        try {
            $summary = $importer->import($path);
        } catch (Throwable $e) {
            $this->error('Import fehlgeschlagen: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->table(
            ['Verarbeitet', 'Angelegt', 'Übersprungen', 'Ungültig', 'Neue Rollen'],
            [[$summary['processed'], $summary['created'], $summary['skipped'], $summary['invalid'], $summary['roles_created']]],
        );

        $this->info('User-Import abgeschlossen.');

        return self::SUCCESS;
    }
}
