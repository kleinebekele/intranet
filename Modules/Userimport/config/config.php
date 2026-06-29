<?php

return [
    'name' => 'Userimport',

    /*
    |--------------------------------------------------------------------------
    | CSV-Pfad
    |--------------------------------------------------------------------------
    |
    | Pfad zur CSV-Datei, die beim täglichen Lauf importiert wird. Über die
    | .env-Variable USERIMPORT_PATH überschreibbar. Relative Pfade werden ab
    | dem Projekt-Root aufgelöst.
    |
    */
    'path' => env('USERIMPORT_PATH', storage_path('app/imports/users.csv')),

    /*
    |--------------------------------------------------------------------------
    | Tägliche Ausführung
    |--------------------------------------------------------------------------
    |
    | Uhrzeit (24h) für den automatischen Import via Scheduler.
    |
    */
    'schedule_time' => env('USERIMPORT_TIME', '03:00'),

    'navigation' => [
        [
            'label' => 'User-Import',
            'route' => 'userimport.index',
            'active' => 'userimport.*',
            'order' => 20,
            'icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.5a6 6 0 00-12 0M9 11a4 4 0 100-8 4 4 0 000 8zM19 8v6m3-3h-6" /></svg>',
        ],
    ],
];
