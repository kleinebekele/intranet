<nav class="sticky top-0 z-20 border-b border-gray-100 bg-white">
    <div class="mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <!-- Left: hamburger (mobile) + page title -->
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = ! sidebarOpen"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none lg:hidden">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="text-sm font-semibold text-gray-700">{{ config('app.name', 'Intranet') }}</span>
        </div>

        <!-- Right: user dropdown -->
        <div class="flex items-center">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center gap-2 rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                        <span class="inline-flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-gray-200 text-xs font-semibold text-gray-600">
                            @if (Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover" />
                            @else
                                {{ Auth::user()->initials }}
                            @endif
                        </span>
                        <div>{{ Auth::user()->name }}</div>
                        <div class="ms-1">
                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profil') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Abmelden') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>
