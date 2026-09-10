<?php

use Illuminate\Support\Facades\Route;
use Modules\ScheduleSlot\App\Http\Controllers\ScheduleSlotController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'prefix' => '',
    'as' => 'manage.',
    'middleware' => ['auth', 'PreventBackHistory']
], function () {
    Route::get('schedule-slot', [ScheduleSlotController::class, 'index'])->name('schedule-slot');
    Route::get('schedule-slot/{id}', [ScheduleSlotController::class, 'show'])->name('schedule-slot.show');
    Route::post('schedule-slot', [ScheduleSlotController::class, 'update'])->name('schedule-slot.update');
});
