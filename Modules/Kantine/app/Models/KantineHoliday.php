<?php

namespace Modules\Kantine\Models;

use Illuminate\Database\Eloquent\Model;

class KantineHoliday extends Model
{
    protected $table = 'kantine_holidays';

    protected $fillable = [
        'date',
        'end_date',
        'name',
        'type',
        'bundesland',
        'year',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'end_date' => 'date',
            'year' => 'integer',
        ];
    }
}
