<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

class ModuleNavigation
{
    /**
     * Collect the navigation entries declared by all enabled modules.
     *
     * Each module may expose a `navigation` array in its `config/config.php`.
     * Every entry supports: label, route (named route) or url, icon (raw SVG),
     * active (route pattern for the active state) and order (sort weight).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function items(): Collection
    {
        return collect(Module::allEnabled())
            ->flatMap(function ($module): array {
                $key = Str::lower($module->getName());

                return collect(config("{$key}.navigation", []))
                    ->map(fn (array $item): array => [
                        'label' => $item['label'] ?? $module->getName(),
                        'route' => $item['route'] ?? null,
                        'url' => $item['url'] ?? null,
                        'icon' => $item['icon'] ?? null,
                        'active' => $item['active'] ?? null,
                        'order' => $item['order'] ?? 100,
                        'module' => $module->getName(),
                    ])
                    ->all();
            })
            ->filter(fn (array $item): bool => filled($item['route']) || filled($item['url']))
            ->sortBy('order')
            ->values();
    }
}
