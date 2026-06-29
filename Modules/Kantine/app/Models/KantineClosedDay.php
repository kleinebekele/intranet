<?php

namespace Modules\Kantine\Models;

use Illuminate\Database\Eloquent\Model;

class KantineClosedDay extends Model
{
    protected $table = 'kantine_closed_days';

    protected $fillable = [
        'date',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
