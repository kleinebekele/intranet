# Modul-/Plugin-Konvention

Dieses Intranet ist modular aufgebaut. Funktionen werden als eigenständige
**Module** (Plugins) entwickelt – z. B. `Kantine`, `UserImport`, Schnittstellen
zu anderen Anwendungen. Grundlage ist das Paket
[`nwidart/laravel-modules`](https://nwidart.com/laravel-modules/).

Diese Datei ist die verbindliche Konvention. **Jeder Beitrag (Mensch oder KI),
der ein Modul erstellt, hält sich an dieses Dokument.**

---

## 1. Grundprinzip

- Jedes Modul lebt in einem eigenen Ordner unter `Modules/<Name>/`.
- Module sind voneinander unabhängig und werden über ihren ServiceProvider
  automatisch entdeckt.
- **Aktive Module erscheinen automatisch in der linken Navigation** – dafür
  deklariert jedes Modul seine Menüpunkte in `config/config.php` (siehe §4).

---

## 2. Neues Modul anlegen

```bash
# Modul-Gerüst erzeugen
php artisan module:make <Name>      # z. B. Kantine  (PascalCase, Singular)

# Autoloader aktualisieren (liest Modules/*/composer.json via merge-plugin ein)
composer dump-autoload
```

Danach ist das Modul bereits `Enabled`. Status prüfen:

```bash
php artisan module:list
```

Aktivieren / deaktivieren:

```bash
php artisan module:enable <Name>
php artisan module:disable <Name>
```

> Ein deaktiviertes Modul verschwindet automatisch aus der Navigation.

---

## 3. Ordnerstruktur eines Moduls

```
Modules/<Name>/
├── app/
│   ├── Http/Controllers/        # Controller (Namespace: Modules\<Name>\Http\Controllers)
│   ├── Models/                  # Eloquent-Modelle
│   └── Providers/               # ServiceProvider, RouteServiceProvider, EventServiceProvider
├── config/
│   └── config.php               # Modul-Konfiguration + Navigation (siehe §4)
├── database/
│   ├── migrations/              # Modul-eigene Migrationen
│   └── seeders/
├── resources/
│   └── views/                   # Blade-Views, angesprochen via "<name>::view"
├── routes/
│   ├── web.php                  # Web-Routen (Session, CSRF)
│   └── api.php                  # API-Routen (Prefix /api)
├── composer.json                # PSR-4-Autoload des Moduls
└── module.json                  # Metadaten (name, alias, providers, priority)
```

---

## 4. Navigation (Pflicht für sichtbare Module)

Damit ein Modul im linken Menü erscheint, definiert es in
`Modules/<Name>/config/config.php` ein `navigation`-Array. Die Konfiguration
wird unter dem **kleingeschriebenen Modulnamen** gemerged, also
`config('<name>.navigation')`.

```php
<?php

return [
    'name' => 'Kantine',

    'navigation' => [
        [
            'label'  => 'Kantine',          // Anzeigetext im Menü
            'route'  => 'kantine.index',    // benannte Route ODER 'url' => '/pfad'
            'active' => 'kantine.*',         // Route-Pattern für den Aktiv-Zustand
            'order'  => 10,                  // Sortiergewicht (klein = weiter oben)
            'icon'   => '<svg class="h-5 w-5" ...>...</svg>', // rohes SVG, optional
        ],
        // weitere Einträge möglich (z. B. Unterseiten)
    ],
];
```

Feldreferenz:

| Feld     | Pflicht | Bedeutung |
|----------|---------|-----------|
| `label`  | ja      | Text im Menü |
| `route`  | ja*     | Name einer registrierten Route (`route()`-Name) |
| `url`    | ja*     | Alternative zu `route`: direkter Pfad/URL |
| `active` | nein    | Route-Pattern (`request()->routeIs(...)`) für die Hervorhebung |
| `order`  | nein    | Sortierung (Default 100) |
| `icon`   | nein    | Rohes SVG-Markup; ohne Angabe wird ein Standard-Icon genutzt |

\* Genau eines von `route` oder `url` muss gesetzt sein.

Die Einträge werden von `App\Support\ModuleNavigation` über
`Module::allEnabled()` eingesammelt und in `resources/views/layouts/sidebar.blade.php`
gerendert. **Es ist kein weiterer Schritt nötig** – kein manuelles Eintragen ins
Hauptmenü.

---

## 5. Routen

`routes/web.php` des Moduls steht unter `auth`/`verified`-Schutz (Standard) und
nutzt benannte Routen mit dem Modulnamen als Präfix:

```php
use Modules\Kantine\Http\Controllers\KantineController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('kantine', [KantineController::class, 'index'])->name('kantine.index');
});
```

Der Routenname (`kantine.index`) wird in der Navigation referenziert.

---

## 6. Views

- Views liegen in `Modules/<Name>/resources/views/` und werden mit dem
  Namespace `"<name>::view"` angesprochen, z. B. `view('kantine::index')`.
- **Für Seiten im Intranet-Layout** das gemeinsame Layout verwenden:

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Kantine</h2>
    </x-slot>

    {{-- Inhalt --}}
</x-app-layout>
```

So erhält die Seite automatisch Sidebar, Navbar und Footer.

---

## 7. Datenbank

- Migrationen des Moduls in `Modules/<Name>/database/migrations/`.
- Ausführen:

```bash
php artisan module:migrate <Name>     # einzelnes Modul
php artisan migrate                    # alle (inkl. Module)
```

---

## 8. Checkliste für ein neues Modul

1. `php artisan module:make <Name>` + `composer dump-autoload`
2. Route(n) in `routes/web.php` definieren und benennen
3. Controller + View(s) anlegen (View nutzt `<x-app-layout>`)
4. `navigation`-Array in `config/config.php` pflegen (§4)
5. Ggf. Migrationen erstellen und ausführen
6. `php artisan test` und `./vendor/bin/pint` laufen lassen
7. Im Browser prüfen: Modul erscheint links im Menü und ist erreichbar

Als vollständiges Referenzbeispiel dient das mitgelieferte Modul
**`Modules/Kantine`**.
