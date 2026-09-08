<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Calendar\Http\Controllers\CalendarController;

/*
|--------------------------------------------------------------------------
| ROUTE MODUL (konvensi core: api/v1 + auth:sanctum)
|--------------------------------------------------------------------------
|   GET    /api/v1/calendar              (list)
|   POST   /api/v1/calendar
|   GET    /api/v1/calendar/{id}
|   PUT    /api/v1/calendar/{id}
|   GET    /api/v1/calendar/{id}/activity-logs
|   DELETE /api/v1/calendar/{id}
*/

Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
    Route::prefix('calendar')->group(function () {
        Route::get('/', [CalendarController::class, 'index']);
        Route::post('/', [CalendarController::class, 'store']);
        Route::get('/{id}', [CalendarController::class, 'show'])->whereNumber('id');
        Route::put('/{id}', [CalendarController::class, 'update'])->whereNumber('id');
        Route::get('/{id}/activity-logs', [CalendarController::class, 'activityLogs'])->whereNumber('id');
        Route::delete('/{id}', [CalendarController::class, 'destroy'])->whereNumber('id');
    });
});
