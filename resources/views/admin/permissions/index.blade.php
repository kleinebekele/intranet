<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Berechtigungen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-md border border-gray-200 bg-white p-4 text-sm text-gray-600 shadow-sm sm:rounded-lg">
                {{ __('Lege je Route fest, welche Rollen sie aufrufen dürfen. Eine Route ohne Häkchen ist für alle eingeloggten Benutzer sichtbar. Administratoren haben immer Zugriff.') }}
            </div>

            @if ($roles->isEmpty())
                <div class="rounded-md border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                    {{ __('Es sind noch keine Rollen vorhanden. Rollen entstehen z.B. über den User-Import.') }}
                </div>
            @endif

            @if ($modules->isEmpty())
                <div class="rounded-md border border-gray-200 bg-white p-6 text-sm text-gray-600 shadow-sm sm:rounded-lg">
                    {{ __('Es sind keine Module mit Routen installiert.') }}
                </div>
            @else
                <form method="POST" action="{{ route('admin.permissions.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @foreach ($modules as $module)
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="border-b border-gray-100 px-6 py-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $module['label'] }}</h3>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left font-medium text-gray-500">{{ __('Route') }}</th>
                                            @foreach ($roles as $role)
                                                <th class="px-4 py-3 text-center font-medium text-gray-500">{{ $role->name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($module['routes'] as $route)
                                            @php $allowed = $permissions->get($route['name'], []); @endphp
                                            <tr>
                                                <td class="px-6 py-3 align-top">
                                                    <div class="font-medium text-gray-900">{{ $route['name'] }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        <span class="font-mono">{{ implode(', ', $route['methods']) }}</span>
                                                        /{{ $route['uri'] }}
                                                    </div>
                                                </td>
                                                @foreach ($roles as $role)
                                                    <td class="px-4 py-3 text-center align-top">
                                                        <input
                                                            type="checkbox"
                                                            name="permissions[{{ $route['name'] }}][]"
                                                            value="{{ $role->id }}"
                                                            @checked(in_array($role->id, $allowed, true))
                                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                        />
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-end">
                        <x-primary-button>{{ __('Speichern') }}</x-primary-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
