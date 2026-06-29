<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Kantine – Verwaltung') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            {{-- Flash-Meldungen --}}
            @if (session('success'))
                <div class="rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="rounded-md border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                    {{ session('warning') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- 1) Öffnungstage & Bundesland                                --}}
            {{-- ============================================================ --}}
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Öffnungstage & Bundesland') }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Wähle die Wochentage, an denen die Kantine regulär geöffnet ist, und das Bundesland für den Feiertags-/Schulferien-Import.') }}
                </p>

                <form method="POST" action="{{ route('kantine.settings.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="bundesland" :value="__('Bundesland')" />
                        <select id="bundesland" name="bundesland"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach ($bundeslaender as $code => $name)
                                <option value="{{ $code }}" @selected($settings->bundesland === $code)>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('bundesland')" />
                    </div>

                    <fieldset class="mt-4">
                        <legend class="text-sm font-medium text-gray-700">{{ __('Geöffnete Wochentage') }}</legend>
                        <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-7">
                            @foreach ([
                                'monday_open' => 'Montag',
                                'tuesday_open' => 'Dienstag',
                                'wednesday_open' => 'Mittwoch',
                                'thursday_open' => 'Donnerstag',
                                'friday_open' => 'Freitag',
                                'saturday_open' => 'Samstag',
                                'sunday_open' => 'Sonntag',
                            ] as $field => $label)
                                <label class="flex items-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50">
                                    <input type="checkbox" name="{{ $field }}" value="1"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @checked($settings->{$field}) />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="pt-2">
                        <x-primary-button>{{ __('Einstellungen speichern') }}</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- ============================================================ --}}
            {{-- 2) Feiertage & Schulferien                                   --}}
            {{-- ============================================================ --}}
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Feiertage & Schulferien') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Importiert automatisch für :state (aktuelles + nächstes Jahr).', ['state' => $bundeslaender[$settings->bundesland] ?? $settings->bundesland]) }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('kantine.holidays.import') }}">
                            @csrf
                            <x-primary-button>{{ __('Jetzt importieren') }}</x-primary-button>
                        </form>
                        @if ($holidays->isNotEmpty())
                            <form method="POST" action="{{ route('kantine.holidays.clear') }}"
                                  onsubmit="return confirm('{{ __('Alle importierten Einträge löschen?') }}')">
                                @csrf
                                @method('DELETE')
                                <x-danger-button type="submit">{{ __('Alle löschen') }}</x-danger-button>
                            </form>
                        @endif
                    </div>
                </div>

                @if ($holidays->isNotEmpty())
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Datum') }}</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Bis') }}</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Name') }}</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Typ') }}</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Jahr') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($holidays as $holiday)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-2">{{ $holiday->date->format('d.m.Y') }}</td>
                                        <td class="whitespace-nowrap px-4 py-2">{{ $holiday->end_date?->format('d.m.Y') ?? '–' }}</td>
                                        <td class="px-4 py-2">{{ $holiday->name }}</td>
                                        <td class="px-4 py-2">
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                                                {{ $holiday->type === 'feiertag' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $holiday->type === 'feiertag' ? __('Feiertag') : __('Schulferien') }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-2">{{ $holiday->year }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mt-4 text-sm text-gray-500">{{ __('Noch keine Feiertage/Schulferien importiert. Klicke auf „Jetzt importieren".') }}</p>
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- 3) Manuelle Schließtage                                      --}}
            {{-- ============================================================ --}}
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Manuelle Schließtage') }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Tage, an denen die Kantine zusätzlich geschlossen bleibt (z. B. Betriebsferien, Sonderschließungen).') }}
                </p>

                <form method="POST" action="{{ route('kantine.closed-days.store') }}" class="mt-4 flex flex-wrap items-end gap-4">
                    @csrf
                    <div>
                        <x-input-label for="closed_date" :value="__('Datum')" />
                        <input id="closed_date" name="date" type="date"
                               min="{{ now()->toDateString() }}"
                               class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               required />
                        <x-input-error class="mt-2" :messages="$errors->get('date')" />
                    </div>
                    <div class="flex-1">
                        <x-input-label for="closed_reason" :value="__('Grund (optional)')" />
                        <x-text-input id="closed_reason" name="reason" type="text" class="mt-1 block w-full" maxlength="255"
                                      placeholder="{{ __('z. B. Betriebsferien') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                    </div>
                    <x-primary-button>{{ __('Hinzufügen') }}</x-primary-button>
                </form>

                @if ($closedDays->isNotEmpty())
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Datum') }}</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Grund') }}</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500">{{ __('Aktion') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($closedDays as $day)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-2">{{ $day->date->format('d.m.Y') }}</td>
                                        <td class="px-4 py-2">{{ $day->reason ?? '–' }}</td>
                                        <td class="whitespace-nowrap px-4 py-2 text-right">
                                            <form method="POST" action="{{ route('kantine.closed-days.destroy', $day) }}"
                                                  onsubmit="return confirm('{{ __('Schließtag wirklich entfernen?') }}')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">
                                                    {{ __('Entfernen') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mt-4 text-sm text-gray-500">{{ __('Keine manuellen Schließtage eingetragen.') }}</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
