<?php

use Illuminate\Support\Facades\Route;
use Modules\Kantine\Http\Controllers\KantineController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('kantine', [KantineController::class, 'index'])->name('kantine.index');
    Route::put('kantine/settings', [KantineController::class, 'updateSettings'])->name('kantine.settings.update');
    Route::post('kantine/holidays/import', [KantineController::class, 'importHolidays'])->name('kantine.holidays.import');
    Route::delete('kantine/holidays', [KantineController::class, 'clearHolidays'])->name('kantine.holidays.clear');
    Route::post('kantine/closed-days', [KantineController::class, 'storeClosedDay'])->name('kantine.closed-days.store');
    Route::delete('kantine/closed-days/{closedDay}', [KantineController::class, 'destroyClosedDay'])->name('kantine.closed-days.destroy');
});
