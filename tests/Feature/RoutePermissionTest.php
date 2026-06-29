<?php

namespace Tests\Feature;

use App\Models\RoutePermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Userimport\Models\Role;
use Tests\TestCase;

class RoutePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_route_without_permission_is_open_to_any_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/userimport')->assertOk();
    }

    public function test_user_without_required_role_is_blocked(): void
    {
        $role = Role::create(['name' => 'admin']);
        RoutePermission::create(['route' => 'userimport.index', 'role_id' => $role->id]);

        $user = User::factory()->create();

        $this->actingAs($user)->get('/userimport')->assertForbidden();
    }

    public function test_user_with_required_role_can_access(): void
    {
        $role = Role::create(['name' => 'admin']);
        RoutePermission::create(['route' => 'userimport.index', 'role_id' => $role->id]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->get('/userimport')->assertOk();
    }

    public function test_admin_can_access_restricted_route_without_role(): void
    {
        $role = Role::create(['name' => 'admin']);
        RoutePermission::create(['route' => 'userimport.index', 'role_id' => $role->id]);

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/userimport')->assertOk();
    }

    public function test_settings_page_is_admin_only(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin/permissions')->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/permissions')->assertOk();
    }

    public function test_admin_can_store_route_permissions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $role = Role::create(['name' => 'editor']);

        $this->actingAs($admin)
            ->put('/admin/permissions', [
                'permissions' => [
                    'userimport.index' => [(string) $role->id],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('route_permissions', [
            'route' => 'userimport.index',
            'role_id' => $role->id,
        ]);
    }

    public function test_updating_permissions_replaces_previous_assignments(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $role = Role::create(['name' => 'editor']);
        RoutePermission::create(['route' => 'userimport.index', 'role_id' => $role->id]);

        $this->actingAs($admin)
            ->put('/admin/permissions', ['permissions' => []])
            ->assertRedirect();

        $this->assertDatabaseMissing('route_permissions', [
            'route' => 'userimport.index',
        ]);
    }

    public function test_make_admin_command_promotes_and_revokes(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->artisan('intranet:make-admin', ['email' => $user->email])
            ->assertSuccessful();
        $this->assertTrue($user->fresh()->isAdmin());

        $this->artisan('intranet:make-admin', ['email' => $user->email, '--revoke' => true])
            ->assertSuccessful();
        $this->assertFalse($user->fresh()->isAdmin());
    }
}
