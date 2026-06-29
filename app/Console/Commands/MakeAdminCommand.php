<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    protected $signature = 'intranet:make-admin {email : E-Mail des Users} {--revoke : Admin-Rechte entziehen}';

    protected $description = 'Macht einen User zum Administrator (oder entzieht die Rechte mit --revoke).';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error("Kein User mit E-Mail {$this->argument('email')} gefunden.");

            return self::FAILURE;
        }

        $user->is_admin = ! $this->option('revoke');
        $user->save();

        $this->info($user->is_admin
            ? "{$user->email} ist jetzt Administrator."
            : "{$user->email} ist kein Administrator mehr.");

        return self::SUCCESS;
    }
}
