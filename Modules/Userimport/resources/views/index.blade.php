<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('User-Import') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            @if ($error)
                <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    {{ $error }}
                </div>
            @endif

            @if ($summary)
                <div class="rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ __('Import abgeschlossen') }}:
                    {{ $summary['processed'] }} {{ __('verarbeitet') }},
                    <strong>{{ $summary['created'] }} {{ __('angelegt') }}</strong>,
                    {{ $summary['skipped'] }} {{ __('übersprungen') }},
                    {{ $summary['invalid'] }} {{ __('ungültig') }},
                    {{ $summary['roles_created'] }} {{ __('neue Rollen') }}.
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">{{ __('Benutzer gesamt') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $userCount }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">{{ __('Rollen gesamt') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $roleCount }}</p>
                </div>
            </div>

            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Import ausführen') }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Lädt neue Benutzer aus einer CSV (Spalten: id, name, first_name, last_name, email, role1–role4). Vorhandene E-Mail-Adressen werden ignoriert.') }}
                </p>

                <dl class="mt-4 space-y-1 text-sm text-gray-600">
                    <div class="flex gap-2">
                        <dt class="font-medium text-gray-700">{{ __('Geplante CSV') }}:</dt>
                        <dd><code class="rounded bg-gray-100 px-1">{{ $path }}</code></dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="font-medium text-gray-700">{{ __('Täglicher Lauf') }}:</dt>
                        <dd>{{ $scheduleTime }} {{ __('Uhr (via Scheduler)') }}</dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('userimport.run') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="file" :value="__('CSV-Datei (optional – sonst wird die geplante CSV verwendet)')" />
                        <input id="file" name="file" type="file" accept=".csv,text/csv"
                               class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700" />
                        <x-input-error class="mt-2" :messages="$errors->get('file')" />
                    </div>

                    <x-primary-button>{{ __('Import jetzt ausführen') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
