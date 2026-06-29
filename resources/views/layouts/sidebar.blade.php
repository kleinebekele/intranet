<aside
    class="fixed inset-y-0 left-0 z-40 w-64 transform bg-gray-800 text-gray-100 transition-transform duration-200 ease-in-out lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="flex h-16 items-center gap-2 px-6 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="block h-8 w-auto fill-current text-white" />
            <span class="text-lg font-semibold">{{ config('app.name', 'Intranet') }}</span>
        </a>
    </div>

    <nav class="px-3 py-4 space-y-1">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10" />
            </svg>
            {{ __('Dashboard') }}
        </x-sidebar-link>

        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            {{ __('Profil') }}
        </x-sidebar-link>

        {{-- Module-Navigation: wird automatisch aus allen aktiven Modulen eingelesen. --}}
        @if (! empty($moduleNavigation) && $moduleNavigation->isNotEmpty())
            <div class="pt-4">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Module') }}</p>

                @foreach ($moduleNavigation as $item)
                    @php
                        $href = $item['route'] && \Illuminate\Support\Facades\Route::has($item['route'])
                            ? route($item['route'])
                            : ($item['url'] ?? '#');
                        $isActive = $item['active'] ? request()->routeIs($item['active']) : false;
                    @endphp

                    <x-sidebar-link :href="$href" :active="$isActive">
                        @if ($item['icon'])
                            {!! $item['icon'] !!}
                        @else
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        @endif
                        {{ $item['label'] }}
                    </x-sidebar-link>
                @endforeach
            </div>
        @endif

        @if (auth()->user()?->isAdmin())
            <div class="pt-4">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Administration') }}</p>

                <x-sidebar-link :href="route('admin.permissions.index')" :active="request()->routeIs('admin.permissions.*')">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z" />
                    </svg>
                    {{ __('Berechtigungen') }}
                </x-sidebar-link>
            </div>
        @endif
    </nav>
</aside>
