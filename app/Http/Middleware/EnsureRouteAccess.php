<?php

namespace App\Http\Middleware;

use App\Support\RouteAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRouteAccess
{
    public function __construct(private readonly RouteAccess $access) {}

    /**
     * Block authenticated users from routes their roles are not allowed to use.
     *
     * Guests are left to the `auth` middleware; unmanaged and core routes stay
     * open. Only routes with explicitly assigned roles are restricted.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $name = $request->route()?->getName();
        $user = $request->user();

        if ($name !== null && $user !== null && ! $this->access->userCanAccess($user, $name)) {
            abort(403, __('Für diesen Bereich fehlt dir die Berechtigung.'));
        }

        return $next($request);
    }
}
