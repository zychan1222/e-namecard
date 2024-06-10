<?php

use App\Http\Controllers\Api\MasterData\RaceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Group all authenticated routes under this middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('master-data')->name('master-data.')->group(function () {
        // Race routes
        Route::prefix('races')->name('races.')->group(function () {
            Route::get('/', [RaceController::class, 'index'])->name('index')->permission('master-race-view');
            Route::post('/', [RaceController::class, 'create'])->name('create')->permission('master-race-create');
            Route::get('{master_race}', [RaceController::class, 'show'])->name('show')->permission('master-race-view');
            Route::put('{master_race}', [RaceController::class, 'update'])->name('update')->permission('master-race-update');
            Route::delete('{master_race}', [RaceController::class, 'destroy'])->name('destroy')->permission('master-race-delete');
        });
    });
});
