<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoutePermission;
use App\Support\RouteAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Userimport\Models\Role;

class PermissionController extends Controller
{
    public function __construct(private readonly RouteAccess $access) {}

    public function index(): View
    {
        return view('admin.permissions.index', [
            'modules' => $this->access->modulesWithRoutes(),
            'roles' => Role::orderBy('name')->get(),
            'permissions' => $this->access->permissionMap(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $managedRoutes = $this->access->managedRouteNames();
        $roleIds = Role::pluck('id')->all();

        $submitted = collect($request->input('permissions', []));

        DB::transaction(function () use ($managedRoutes, $roleIds, $submitted): void {
            RoutePermission::whereIn('route', $managedRoutes)->delete();

            foreach ($managedRoutes as $route) {
                $selected = collect($submitted->get($route, []))
                    ->map(fn ($id): int => (int) $id)
                    ->filter(fn (int $id): bool => in_array($id, $roleIds, true))
                    ->unique();

                foreach ($selected as $id) {
                    RoutePermission::create(['route' => $route, 'role_id' => $id]);
                }
            }
        });

        return back()->with('status', __('Berechtigungen gespeichert.'));
    }
}
