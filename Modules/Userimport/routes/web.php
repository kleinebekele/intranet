<?php

use Illuminate\Support\Facades\Route;
use Modules\Userimport\Http\Controllers\UserimportController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('userimport', [UserimportController::class, 'index'])->name('userimport.index');
    Route::post('userimport/run', [UserimportController::class, 'run'])->name('userimport.run');
});
