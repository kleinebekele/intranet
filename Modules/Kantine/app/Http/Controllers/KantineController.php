<?php

namespace Modules\Kantine\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Kantine\Models\KantineClosedDay;
use Modules\Kantine\Models\KantineHoliday;
use Modules\Kantine\Models\KantineSetting;
use Modules\Kantine\Services\HolidayImportService;

class KantineController extends Controller
{
    /**
     * Übersicht / Dashboard der Kantine.
     */
    public function index(): View
    {
        $settings = KantineSetting::current();

        $holidays = KantineHoliday::where('bundesland', $settings->bundesland)
            ->orderBy('date')
            ->get();

        $closedDays = KantineClosedDay::orderBy('date')->get();

        return view('kantine::index', [
            'settings' => $settings,
            'holidays' => $holidays,
            'closedDays' => $closedDays,
            'bundeslaender' => KantineSetting::BUNDESLAENDER,
        ]);
    }

    /**
     * Einstellungen speichern (Wochentage + Bundesland).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bundesland' => ['required', 'string', 'size:2', 'in:'.implode(',', array_keys(KantineSetting::BUNDESLAENDER))],
            'monday_open' => ['nullable'],
            'tuesday_open' => ['nullable'],
            'wednesday_open' => ['nullable'],
            'thursday_open' => ['nullable'],
            'friday_open' => ['nullable'],
            'saturday_open' => ['nullable'],
            'sunday_open' => ['nullable'],
        ]);

        $settings = KantineSetting::current();

        $days = ['monday_open', 'tuesday_open', 'wednesday_open', 'thursday_open', 'friday_open', 'saturday_open', 'sunday_open'];

        foreach ($days as $day) {
            $settings->{$day} = isset($validated[$day]);
        }

        $settings->bundesland = $validated['bundesland'];
        $settings->save();

        return redirect()->route('kantine.index')
            ->with('success', 'Einstellungen gespeichert.');
    }

    /**
     * Feiertage und Schulferien importieren.
     */
    public function importHolidays(HolidayImportService $service): RedirectResponse
    {
        $settings = KantineSetting::current();
        $result = $service->import($settings->bundesland);

        $message = "Import abgeschlossen: {$result['feiertage']} Feiertage, {$result['schulferien']} Schulferien.";

        if (! empty($result['errors'])) {
            $message .= ' Fehler: '.implode('; ', $result['errors']);

            return redirect()->route('kantine.index')
                ->with('warning', $message);
        }

        return redirect()->route('kantine.index')
            ->with('success', $message);
    }

    /**
     * Manuellen Schließtag hinzufügen.
     */
    public function storeClosedDay(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $exists = KantineClosedDay::where('date', $validated['date'])->exists();

        if ($exists) {
            return redirect()->route('kantine.index')
                ->with('error', 'Dieser Tag ist bereits als geschlossen eingetragen.');
        }

        KantineClosedDay::create([
            'date' => $validated['date'],
            'reason' => $validated['reason'] ?? null,
        ]);

        return redirect()->route('kantine.index')
            ->with('success', 'Schließtag hinzugefügt.');
    }

    /**
     * Manuellen Schließtag entfernen.
     */
    public function destroyClosedDay(KantineClosedDay $closedDay): RedirectResponse
    {
        $closedDay->delete();

        return redirect()->route('kantine.index')
            ->with('success', 'Schließtag entfernt.');
    }

    /**
     * Alle importierten Feiertage/Schulferien löschen.
     */
    public function clearHolidays(): RedirectResponse
    {
        $settings = KantineSetting::current();

        KantineHoliday::where('bundesland', $settings->bundesland)->delete();

        return redirect()->route('kantine.index')
            ->with('success', 'Alle importierten Feiertage und Schulferien wurden gelöscht.');
    }
}
