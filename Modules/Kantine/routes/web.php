<?php

use Illuminate\Support\Facades\Route;
use Modules\Kantine\Http\Controllers\KantineController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('kantines', KantineController::class)->names('kantine');
});
