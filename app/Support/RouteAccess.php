<?php

namespace App\Support;

use App\Models\RoutePermission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

class RouteAccess
{
    /**
     * Named routes grouped by the enabled module they belong to.
     *
     * A route is considered part of a module when its name is prefixed with the
     * lower-cased module name (convention: `<module>.<action>`).
     *
     * @return Collection<int, array{module: string, label: string, routes: array<int, array{name: string, methods: array<int, string>, uri: string}>}>
     */
    public function modulesWithRoutes(): Collection
    {
        $routesByPrefix = $this->namedModuleRoutes();

        return collect(Module::allEnabled())
            ->map(function ($module) use ($routesByPrefix): array {
                $key = Str::lower($module->getName());

                return [
                    'module' => $module->getName(),
                    'label' => config("{$key}.name", $module->getName()),
                    'routes' => $routesByPrefix->get($key, collect())->values()->all(),
                ];
            })
            ->filter(fn (array $entry): bool => $entry['routes'] !== [])
            ->sortBy('label')
            ->values();
    }

    /**
     * All named routes that belong to an enabled module, keyed by module name.
     *
     * @return Collection<string, Collection<int, array{name: string, methods: array<int, string>, uri: string}>>
     */
    protected function namedModuleRoutes(): Collection
    {
        $moduleKeys = collect(Module::allEnabled())
            ->map(fn ($module): string => Str::lower($module->getName()))
            ->all();

        return collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn ($route): bool => filled($route->getName()))
            ->map(fn ($route): array => [
                'name' => $route->getName(),
                'methods' => array_values(array_diff($route->methods(), ['HEAD'])),
                'uri' => $route->uri(),
            ])
            ->filter(fn (array $r): bool => in_array(Str::before($r['name'], '.'), $moduleKeys, true))
            ->groupBy(fn (array $r): string => Str::before($r['name'], '.'));
    }

    /**
     * Names of the routes that are managed by the permission system.
     *
     * @return array<int, string>
     */
    public function managedRouteNames(): array
    {
        return $this->modulesWithRoutes()
            ->flatMap(fn (array $entry): array => array_column($entry['routes'], 'name'))
            ->all();
    }

    /**
     * Role ids permitted to access a route, keyed by route name.
     *
     * @return Collection<string, array<int, int>>
     */
    public function permissionMap(): Collection
    {
        return RoutePermission::query()
            ->get(['route', 'role_id'])
            ->groupBy('route')
            ->map(fn (Collection $rows): array => $rows->pluck('role_id')->map(fn ($id): int => (int) $id)->all());
    }

    /**
     * Whether the given user may access the named route.
     *
     * Admins may access everything. A route without any assigned role is open to
     * every authenticated user; once roles are assigned only those roles pass.
     */
    public function userCanAccess(?User $user, string $routeName): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $allowedRoleIds = RoutePermission::query()
            ->where('route', $routeName)
            ->pluck('role_id')
            ->all();

        if ($allowedRoleIds === []) {
            return true;
        }

        return $user->roles()->whereIn('roles.id', $allowedRoleIds)->exists();
    }
}
