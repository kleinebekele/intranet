<?php

namespace Modules\Userimport\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Userimport\Models\Role;
use RuntimeException;

class UserImporter
{
    /**
     * Required CSV header columns.
     *
     * @var list<string>
     */
    private const REQUIRED_COLUMNS = ['id', 'name', 'first_name', 'last_name', 'email'];

    /**
     * Import users from a CSV file.
     *
     * New users are created only when their email does not yet exist; existing
     * users are completely ignored. The `id` column is stored as `external_id`.
     * Values in role1..role4 are resolved to roles (created on demand) and
     * attached to the user via the n:n relationship.
     *
     * @return array{created:int, skipped:int, invalid:int, roles_created:int, processed:int}
     */
    public function import(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException("CSV-Datei nicht gefunden oder nicht lesbar: {$path}");
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException("CSV-Datei konnte nicht geöffnet werden: {$path}");
        }

        $summary = ['created' => 0, 'skipped' => 0, 'invalid' => 0, 'roles_created' => 0, 'processed' => 0];

        try {
            $map = $this->readHeader($handle);

            while (($row = fgetcsv($handle)) !== false) {
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $summary['processed']++;

                $email = strtolower(trim((string) $this->value($row, $map, 'email')));

                if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $summary['invalid']++;

                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $summary['skipped']++;

                    continue;
                }

                $user = User::create([
                    'external_id' => trim((string) $this->value($row, $map, 'id')) ?: null,
                    'name' => trim((string) $this->value($row, $map, 'name')),
                    'first_name' => trim((string) $this->value($row, $map, 'first_name')) ?: null,
                    'last_name' => trim((string) $this->value($row, $map, 'last_name')) ?: null,
                    'email' => $email,
                    'password' => Hash::make(Str::password(32)),
                ]);

                $this->syncRoles($user, $row, $map, $summary);

                $summary['created']++;
            }
        } finally {
            fclose($handle);
        }

        return $summary;
    }

    /**
     * Read and validate the header row, returning a column-name => index map.
     *
     * @return array<string, int>
     */
    private function readHeader($handle): array
    {
        $header = fgetcsv($handle);

        if ($header === false) {
            throw new RuntimeException('CSV-Datei ist leer.');
        }

        // Strip a possible UTF-8 BOM from the first column.
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);

        $map = [];
        foreach ($header as $index => $name) {
            $map[strtolower(trim((string) $name))] = $index;
        }

        $missing = array_diff(self::REQUIRED_COLUMNS, array_keys($map));

        if ($missing !== []) {
            throw new RuntimeException('Fehlende Spalten in der CSV: '.implode(', ', $missing));
        }

        return $map;
    }

    /**
     * Resolve role1..role4 for a row and attach them to the user.
     *
     * @param  array<int, string|null>  $row
     * @param  array<string, int>  $map
     * @param  array{created:int, skipped:int, invalid:int, roles_created:int, processed:int}  $summary
     */
    private function syncRoles(User $user, array $row, array $map, array &$summary): void
    {
        $roleIds = [];

        foreach (['role1', 'role2', 'role3', 'role4'] as $column) {
            $name = trim((string) $this->value($row, $map, $column));

            if ($name === '') {
                continue;
            }

            $role = Role::firstOrCreate(['name' => $name]);

            if ($role->wasRecentlyCreated) {
                $summary['roles_created']++;
            }

            $roleIds[] = $role->id;
        }

        if ($roleIds !== []) {
            $user->roles()->syncWithoutDetaching($roleIds);
        }
    }

    /**
     * Read a named column from a row using the header map.
     *
     * @param  array<int, string|null>  $row
     * @param  array<string, int>  $map
     */
    private function value(array $row, array $map, string $column): ?string
    {
        if (! array_key_exists($column, $map)) {
            return null;
        }

        return $row[$map[$column]] ?? null;
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        return count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0;
    }
}
