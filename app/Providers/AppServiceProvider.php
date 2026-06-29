<?php

namespace App\Providers;

use App\Support\ModuleNavigation;
use App\Support\RouteAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.sidebar', function (\Illuminate\View\View $view): void {
            $user = Auth::user();
            $access = app(RouteAccess::class);

            $items = app(ModuleNavigation::class)->items()
                ->filter(fn (array $item): bool => blank($item['route'])
                    || $access->userCanAccess($user, $item['route']))
                ->values();

            $view->with('moduleNavigation', $items);
        });
    }
}
