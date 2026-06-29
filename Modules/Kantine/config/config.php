<?php

return [
    'name' => 'Kantine',

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Diese Einträge werden automatisch in die linke Hauptnavigation des
    | Intranets eingelesen (siehe App\Support\ModuleNavigation). Jeder Eintrag
    | unterstützt: label, route (oder url), icon (rohes SVG), active
    | (Route-Pattern für den Aktiv-Zustand) und order (Sortierung).
    |
    */
    'navigation' => [
        [
            'label' => 'Kantine',
            'route' => 'kantine.index',
            'active' => 'kantine.*',
            'order' => 10,
            'icon' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 3v18M8 3v6a2 2 0 01-4 0V3M16 3c-1.5 0-3 1.8-3 5 0 2.5 1 3.5 2 3.8V21h2V3z" /></svg>',
        ],
    ],
];
