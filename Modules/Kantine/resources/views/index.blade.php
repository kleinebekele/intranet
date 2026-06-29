<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Kantine') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">{{ __('Speiseplan dieser Woche') }}</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Dies ist ein Beispiel-Modul. Es demonstriert, wie ein Plugin automatisch in der linken Navigation erscheint.') }}
                    </p>

                    <ul class="mt-4 divide-y divide-gray-100">
                        @foreach ([
                            ['tag' => 'Montag', 'gericht' => 'Spaghetti Bolognese', 'preis' => '4,50 €'],
                            ['tag' => 'Dienstag', 'gericht' => 'Gemüsecurry mit Reis', 'preis' => '4,20 €'],
                            ['tag' => 'Mittwoch', 'gericht' => 'Schnitzel mit Pommes', 'preis' => '5,10 €'],
                            ['tag' => 'Donnerstag', 'gericht' => 'Lachsfilet mit Kartoffeln', 'preis' => '5,80 €'],
                            ['tag' => 'Freitag', 'gericht' => 'Pizza Margherita', 'preis' => '3,90 €'],
                        ] as $eintrag)
                            <li class="flex items-center justify-between py-3">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $eintrag['tag'] }}</span>
                                    <span class="ms-2 text-gray-600">{{ $eintrag['gericht'] }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">{{ $eintrag['preis'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
