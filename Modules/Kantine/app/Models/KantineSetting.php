<?php

namespace Modules\Kantine\Models;

use Illuminate\Database\Eloquent\Model;

class KantineSetting extends Model
{
    protected $table = 'kantine_settings';

    protected $fillable = [
        'bundesland',
        'monday_open',
        'tuesday_open',
        'wednesday_open',
        'thursday_open',
        'friday_open',
        'saturday_open',
        'sunday_open',
    ];

    protected function casts(): array
    {
        return [
            'monday_open' => 'boolean',
            'tuesday_open' => 'boolean',
            'wednesday_open' => 'boolean',
            'thursday_open' => 'boolean',
            'friday_open' => 'boolean',
            'saturday_open' => 'boolean',
            'sunday_open' => 'boolean',
        ];
    }

    /**
     * Bundesland-Kürzel → Name.
     */
    public const BUNDESLAENDER = [
        'BW' => 'Baden-Württemberg',
        'BY' => 'Bayern',
        'BE' => 'Berlin',
        'BB' => 'Brandenburg',
        'HB' => 'Bremen',
        'HH' => 'Hamburg',
        'HE' => 'Hessen',
        'MV' => 'Mecklenburg-Vorpommern',
        'NI' => 'Niedersachsen',
        'NW' => 'Nordrhein-Westfalen',
        'RP' => 'Rheinland-Pfalz',
        'SL' => 'Saarland',
        'SN' => 'Sachsen',
        'ST' => 'Sachsen-Anhalt',
        'SH' => 'Schleswig-Holstein',
        'TH' => 'Thüringen',
    ];

    /**
     * Gibt die einzige Einstellungszeile zurück (erstellt sie bei Bedarf).
     */
    public static function current(): self
    {
        return self::firstOrCreate([], ['bundesland' => 'NW']);
    }

    /**
     * Prüft ob ein bestimmter Wochentag geöffnet ist (1=Mo … 7=So).
     */
    public function isDayOpen(int $dayOfWeek): bool
    {
        return match ($dayOfWeek) {
            1 => $this->monday_open,
            2 => $this->tuesday_open,
            3 => $this->wednesday_open,
            4 => $this->thursday_open,
            5 => $this->friday_open,
            6 => $this->saturday_open,
            7 => $this->sunday_open,
            default => false,
        };
    }
}
