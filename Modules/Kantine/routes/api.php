<?php

use Illuminate\Support\Facades\Route;
use Modules\Kantine\Http\Controllers\KantineController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('kantines', KantineController::class)->names('kantine');
});
