<?php

use Illuminate\Support\Facades\Route;
use Modules\Webinar\App\Http\Controllers\WebinarController;

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
    Route::get('webinar/customers', [WebinarController::class, 'index'])->name('webinar');
    Route::post('webinar-customer-delete/{id}', [WebinarController::class, 'webinarEventDeleteCustomer'])->name('webinar.customer.delete');
    Route::post('/webinar-users/join-community/{id}', [WebinarController::class, 'webinarJoinCommunity'])->name('webinar.customer.joinCommunity');
    Route::get('/webinar/customer/{id}',  [WebinarController::class, 'showcustomer'])->name('webinar.customer.show');
    Route::post('webinar-users/acc-deactivate', [WebinarController::class, 'accDeactivate'])->name('webinar.customer.accDeactivate');
    Route::post('/webinar-users/acc-delete', [WebinarController::class, 'accDelete'])->name('webinar.customer.accDelete');

    Route::get('webinar/leads', [WebinarController::class, 'webinarLeads'])->name('webinar.leads');
    Route::post('webinar-lead-delete/{id}', [WebinarController::class, 'webinarEventDeleteLead'])->name('webinar.lead.delete');
    Route::get('/webinar/leads/{id}',  [WebinarController::class, 'showLeads'])->name('webinar.leads.show');
    Route::post('webinar-users/acc-deactivate/{id}', [WebinarController::class, 'accDeactivate'])->name('webinar.leads.accDeactivate');
    Route::post('/webinar-users/acc-delete/{id}', [WebinarController::class, 'accDelete'])->name('webinar.leads.accDelete');

    Route::get('program-list', [WebinarController::class, 'webinarEventDetails'])->name('webinar.event.details');
    Route::get('webinar-event-create', [WebinarController::class, 'webinarEventCreate'])->name('webinar.event.create');
    Route::post('webinar-event-store', [WebinarController::class, 'webinarEventStore'])->name('webinar.event.store');
    Route::get('webinar-event-edit/{id}', [WebinarController::class, 'webinarEventEdit'])->name('webinar.event.edit');
    Route::post('webinar-event-update/{id}', [WebinarController::class, 'webinarEventUpdate'])->name('webinar.event.update');
    Route::get('webinar-onboard-detail', [WebinarController::class, 'webinarOnboardDetails'])->name('webinar.onboard.detail');

    Route::post('/program-toggle-status/{id}', [WebinarController::class, 'toggleStatus'])->name('webinar.toggleStatus');

    Route::get('webinar/attend_detail', [WebinarController::class, 'webinarAttend'])->name('webinar.attend');
    Route::post('/webinar-users/attend-status/{id}', [WebinarController::class, 'webinarStatus'])->name('webinar.customer.attend.status');
    Route::post('/webinar-users/isdnd-status/{id}', [WebinarController::class, 'isDndStatus'])->name('webinar.customer.isdnd.status');
});


Route::group([
    'prefix' => '',
    'as' => 'manage.',
    'middleware' => ['auth', 'PreventBackHistory']
], function () {
    Route::get('workshop/customers', [WebinarController::class, 'workshopCustomer'])->name('workshop.customer');
    Route::post('workshop-customer-delete/{id}', [WebinarController::class, 'workshopDeleteCustomer'])->name('workshop.customer.delete');

    Route::get('workshop/leads', [WebinarController::class, 'workshopLeads'])->name('workshop.leads');
    Route::post('workshop-lead-delete/{id}', [WebinarController::class, 'workshopDeleteLead'])->name('workshop.lead.delete');
});
