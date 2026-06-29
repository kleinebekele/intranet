# KI-Prompt: Neues Plugin für das Intranet bauen

> **Zweck:** Diese Datei ist ein fertiger Prompt. Kopiere den Abschnitt
> [„📋 Prompt zum Kopieren"](#-prompt-zum-kopieren) in eine neue Devin-Session
> und ergänze unten deine Plugin-Beschreibung. Damit baut jede KI ein neues
> Modul/Plugin nach den verbindlichen Konventionen dieses Projekts.

---

## Projektstand (Stand: dieser Commit)

**Repo:** `kleinebekele/intranet` · **Stack:** Laravel 13 (PHP 8.3), Blade +
Tailwind + Alpine, Vite, SQLite lokal / MariaDB auf dem Plesk-Server.

Bereits vorhanden:

- **Auth & Profil** (Laravel Breeze): Login, Registrierung, Passwort-Reset,
  E-Mail-Verifizierung; erweitertes Profil (Vorname/Nachname, Avatar,
  Benachrichtigungen).
- **2-Spalten-Layout** (`<x-app-layout>`): links Sidebar, oben Navbar, unten
  Footer, responsive. Sprache: **Deutsch** (`APP_LOCALE=de`, Übersetzungen in
  `lang/de/`).
- **Modul-System** (`nwidart/laravel-modules`): Plugins liegen unter
  `Modules/<Name>/` und erscheinen **automatisch** in der Sidebar (siehe
  `docs/MODULE-KONVENTION.md`). Referenz-Module: `Modules/Kantine` (Demo) und
  `Modules/Userimport` (echtes Plugin: CSV-Import, `external_id`, n:n-Rollen,
  täglicher Scheduler-Lauf).
- **Rollen & Berechtigungen:** Tabelle `roles` + Pivot `role_user` (User n:n
  Rollen). Admin-Seite **„Berechtigungen"** (`/admin/permissions`, nur Admins)
  legt **je Route** fest, welche Rollen sie aufrufen dürfen. Durchgesetzt via
  Middleware `App\Http\Middleware\EnsureRouteAccess`; Logik in
  `App\Support\RouteAccess`. Sidebar blendet nicht erlaubte Einträge aus.
  - Route **ohne** Regel → für alle eingeloggten User offen.
  - Route **mit** Regel → nur zugewiesene Rollen. **Admins immer.**
  - Admin werden: `php artisan intranet:make-admin <email>` (`--revoke` entzieht).
- **Deploy:** `./deploy.sh` (Plesk) – `git pull` + Composer + Migrationen +
  Build + Cache. Geplante Aufgaben brauchen **einmalig** einen Cronjob:
  `* * * * * php /pfad/artisan schedule:run >> /dev/null 2>&1`.

### Wichtige Dateien

| Zweck | Pfad |
|-------|------|
| Modul-Konvention (verbindlich) | `docs/MODULE-KONVENTION.md` |
| Sidebar / Auto-Navigation | `app/Support/ModuleNavigation.php`, `resources/views/layouts/sidebar.blade.php` |
| Rollen-Zugriff je Route | `app/Support/RouteAccess.php`, `app/Http/Middleware/EnsureRouteAccess.php` |
| Admin-Einstellungen | `app/Http/Controllers/Admin/PermissionController.php`, `resources/views/admin/permissions/` |
| Role-Modell | `Modules/Userimport/app/Models/Role.php` |
| Referenz-Plugin | `Modules/Userimport/` |

---

## Arbeitsweise (für die KI verbindlich)

1. **Schritt für Schritt.** Nach jedem fertigen Schritt anhalten und den Stand
   sichern. Der Nutzer sagt „weiter" / „fertig".
2. **Git-Workflow:** pro Schritt ein kurzer Branch → **Pull Request**. Direktes
   Pushen auf `main` und das Mergen erledigt der Nutzer (Auto-Merge ist aktiv).
   Niemals selbst mergen.
3. **Vor jedem PR:** `php artisan test` (alle grün) **und**
   `./vendor/bin/pint` (sauber) **und** `npm run build`.
4. **Im Browser prüfen:** einloggen (`test@intranet.local` / `password`),
   Modul links im Menü öffnen, Hauptfunktion testen.
5. **Konvention einhalten:** strikt nach `docs/MODULE-KONVENTION.md` arbeiten;
   `Modules/Userimport` als lebendes Beispiel nehmen.

---

## Schritt-für-Schritt: neues Plugin

```bash
# 1) Gerüst
php artisan module:make <Name>        # PascalCase, Singular, z. B. Urlaub
composer dump-autoload

# 2) routes/web.php des Moduls: benannte Routen mit Modul-Präfix
#    Route::middleware(['auth','verified'])->group(fn() =>
#        Route::get('<name>', [<Name>Controller::class,'index'])->name('<name>.index'));

# 3) Controller + View (View nutzt <x-app-layout>, Namespace "<name>::index")

# 4) Navigation in Modules/<Name>/config/config.php pflegen (label, route, icon, order)

# 5) Bei Datenbank: Migration in Modules/<Name>/database/migrations + migrate

# 6) Optional: Artisan-Command + Scheduler im ServiceProvider (siehe §7a der Konvention)

# 7) Prüfen
php artisan test && ./vendor/bin/pint --test && npm run build
php artisan migrate
```

**Rollen/Rechte:** Neue Modul-Routen werden automatisch auf der Seite
`/admin/permissions` gelistet (Erkennung über Routennamen-Präfix `<name>.`).
Dort kann der Admin je Route Rollen vergeben – im Plugin selbst ist dafür **kein
Code** nötig.

### Checkliste vor dem PR

- [ ] Modul erscheint links im Menü und ist erreichbar
- [ ] Routen heißen `<name>.<aktion>` und stehen unter `auth`/`verified`
- [ ] View nutzt `<x-app-layout>`, Texte deutsch (ggf. `__()` + `lang/de`)
- [ ] Migrationen laufen sauber (`php artisan migrate`)
- [ ] `php artisan test` grün, `./vendor/bin/pint` sauber, `npm run build` ok
- [ ] PR mit aussagekräftiger Beschreibung erstellt

---

## 📋 Prompt zum Kopieren

```text
Du baust ein neues Plugin/Modul für das Laravel-Intranet (Repo kleinebekele/intranet).

Halte dich strikt an docs/MODULE-KONVENTION.md und docs/KI-PLUGIN-PROMPT.md und
nutze Modules/Userimport als Referenz. Wichtige Regeln:
- Modul unter Modules/<Name>/ anlegen (php artisan module:make + composer dump-autoload).
- Routen benannt als <name>.<aktion>, unter auth/verified; Navigation in
  Modules/<Name>/config/config.php deklarieren (erscheint automatisch in der Sidebar).
- Views mit <x-app-layout>, Texte auf Deutsch.
- Rollen/Rechte je Route laufen automatisch über /admin/permissions – kein eigener Code nötig.
- Arbeite Schritt für Schritt; pro Schritt ein Branch + Pull Request (nicht selbst mergen,
  Auto-Merge ist aktiv). Vor jedem PR: php artisan test, ./vendor/bin/pint, npm run build,
  und im Browser prüfen (Login test@intranet.local / password).

Neues Plugin:
- Name: <PluginName>
- Zweck: <was soll es tun?>
- Daten/Tabellen: <falls nötig>
- Rollen/Sichtbarkeit: <wer darf was? sonst Standard>
- Besonderheiten: <z. B. Import, Scheduler, externe Schnittstelle>
```
