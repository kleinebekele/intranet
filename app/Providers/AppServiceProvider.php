<?php

namespace App\Providers;

use App\Support\ModuleNavigation;
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
            $view->with('moduleNavigation', app(ModuleNavigation::class)->items());
        });
    }
}
