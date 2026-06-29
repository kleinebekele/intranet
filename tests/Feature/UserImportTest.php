<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Userimport\Models\Role;
use Modules\Userimport\Services\UserImporter;
use Tests\TestCase;

class UserImportTest extends TestCase
{
    use RefreshDatabase;

    private function csv(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'userimport_').'.csv';
        file_put_contents($path, $contents);

        return $path;
    }

    public function test_new_users_are_created_with_external_id_and_roles(): void
    {
        $path = $this->csv(
            "id,name,first_name,last_name,email,role1,role2,role3,role4\n".
            "1001,Max Mustermann,Max,Mustermann,max@example.com,admin,editor,,\n"
        );

        $summary = app(UserImporter::class)->import($path);

        $this->assertSame(1, $summary['created']);
        $this->assertSame(2, $summary['roles_created']);

        $user = User::where('email', 'max@example.com')->firstOrFail();
        $this->assertSame('1001', $user->external_id);
        $this->assertSame('Max', $user->first_name);
        $this->assertSame('Mustermann', $user->last_name);

        $this->assertEqualsCanonicalizing(
            ['admin', 'editor'],
            $user->roles()->pluck('name')->all(),
        );
    }

    public function test_existing_email_is_completely_ignored(): void
    {
        $existing = User::factory()->create(['email' => 'taken@example.com', 'name' => 'Original']);

        $path = $this->csv(
            "id,name,first_name,last_name,email,role1,role2,role3,role4\n".
            "999,Neuer Name,Neu,Name,taken@example.com,admin,,,\n"
        );

        $summary = app(UserImporter::class)->import($path);

        $this->assertSame(0, $summary['created']);
        $this->assertSame(1, $summary['skipped']);

        $existing->refresh();
        $this->assertSame('Original', $existing->name);
        $this->assertNull($existing->external_id);
        $this->assertCount(0, $existing->roles);
        $this->assertSame(0, Role::count());
    }

    public function test_invalid_email_is_skipped_and_empty_roles_create_no_assignment(): void
    {
        $path = $this->csv(
            "id,name,first_name,last_name,email,role1,role2,role3,role4\n".
            "1,No Mail,No,Mail,not-an-email,admin,,,\n".
            "2,Role Less,Role,Less,roleless@example.com,,,,\n"
        );

        $summary = app(UserImporter::class)->import($path);

        $this->assertSame(1, $summary['created']);
        $this->assertSame(1, $summary['invalid']);
        $this->assertSame(0, $summary['roles_created']);

        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
        $user = User::where('email', 'roleless@example.com')->firstOrFail();
        $this->assertCount(0, $user->roles);
    }

    public function test_shared_role_is_reused_across_users(): void
    {
        $path = $this->csv(
            "id,name,first_name,last_name,email,role1,role2,role3,role4\n".
            "1,A,A,A,a@example.com,admin,,,\n".
            "2,B,B,B,b@example.com,admin,,,\n"
        );

        $summary = app(UserImporter::class)->import($path);

        $this->assertSame(2, $summary['created']);
        $this->assertSame(1, $summary['roles_created']);
        $this->assertSame(1, Role::where('name', 'admin')->count());
    }

    public function test_command_runs_with_file_option(): void
    {
        $path = $this->csv(
            "id,name,first_name,last_name,email,role1,role2,role3,role4\n".
            "5,Cmd User,Cmd,User,cmd@example.com,viewer,,,\n"
        );

        $this->artisan('userimport:run', ['--file' => $path])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'cmd@example.com', 'external_id' => '5']);
    }
}
