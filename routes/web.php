<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\BusinessPanelController;
use App\Http\Controllers\BusinessEmployeeController;
use App\Http\Controllers\BusinessServiceController;
use App\Http\Controllers\BusinessEmployeeAvailabilityController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\BusinessAppointmentController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Reservas públicas para clientes
Route::prefix('reservar/{business:slug}')
    ->name('booking.')
    ->group(function () {
        Route::get('/', [PublicBookingController::class, 'show'])
            ->name('show');

        Route::get('/horarios', [PublicBookingController::class, 'availability'])
            ->name('availability');

        Route::post('/', [PublicBookingController::class, 'store'])
            ->name('store');
    });

Route::middleware('auth')->group(function () {
    Route::get('/panel', [BusinessPanelController::class, 'index'])
        ->middleware('active-business')
        ->name('panel');

    Route::get('/negocio/citas', [BusinessAppointmentController::class, 'index'])
    ->middleware('active-business')
    ->name('business.appointments.index');

    Route::patch(
        '/negocio/citas/{appointment}/estado',
        [BusinessAppointmentController::class, 'updateStatus']
    )
        ->whereNumber('appointment')
        ->middleware('active-business')
        ->name('business.appointments.update-status');

    Route::prefix('negocio/empleados')
    ->name('business.employees.')
    ->middleware('active-business')
    ->group(function () {
        Route::get('/', [BusinessEmployeeController::class, 'index'])
            ->name('index');

        Route::get('/crear', [BusinessEmployeeController::class, 'create'])
            ->name('create');

        Route::post('/', [BusinessEmployeeController::class, 'store'])
            ->name('store');

        Route::get('/{employee}/editar', [BusinessEmployeeController::class, 'edit'])
            ->whereNumber('employee')
            ->name('edit');

        Route::put('/{employee}', [BusinessEmployeeController::class, 'update'])
            ->whereNumber('employee')
            ->name('update');

        Route::patch('/{employee}/estado', [
            BusinessEmployeeController::class,
            'toggleStatus',
        ])->whereNumber('employee')->name('toggle-status');

        Route::get('/{employee}/disponibilidad', [
            BusinessEmployeeAvailabilityController::class,
            'index',
        ])->whereNumber('employee')->name('availability.index');

        Route::patch('/{employee}/disponibilidad/{slot}', [
            BusinessEmployeeAvailabilityController::class,
            'toggle',
        ])->whereNumber('employee')
        ->whereNumber('slot')
        ->name('availability.toggle');
    });


    Route::prefix('negocio/servicios')
        ->name('business.services.')
        ->middleware('active-business')
        ->group(function () {
            Route::get('/', [BusinessServiceController::class, 'index'])
                ->name('index');

            Route::get('/crear', [BusinessServiceController::class, 'create'])
                ->name('create');

            Route::post('/', [BusinessServiceController::class, 'store'])
                ->name('store');

            Route::get('/{service}/editar', [BusinessServiceController::class, 'edit'])
                ->whereNumber('service')
                ->name('edit');

            Route::put('/{service}', [BusinessServiceController::class, 'update'])
                ->whereNumber('service')
                ->name('update');

            Route::patch('/{service}/estado', [BusinessServiceController::class, 'toggleStatus'])
                ->whereNumber('service')
                ->name('toggle-status');
        });


    Route::get('/negocio/{business}', [BusinessPanelController::class, 'show'])
        ->middleware('active-business')
        ->name('business.dashboard');


    Route::post('/logout', function () {
        auth()->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

Route::middleware(['auth', 'superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');

        Route::get('/negocios', [BusinessController::class, 'index'])
            ->name('businesses.index');

        Route::get('/negocios/crear', [BusinessController::class, 'create'])
            ->name('businesses.create');

        Route::get('/negocios/{business}/editar', [BusinessController::class, 'edit'])
            ->name('businesses.edit');

        Route::put('/negocios/{business}', [BusinessController::class, 'update'])
            ->name('businesses.update');

        Route::post('/negocios', [BusinessController::class, 'store'])
            ->name('businesses.store');
        Route::patch(
            '/negocios/{business}/estado',
            [BusinessController::class, 'toggleStatus']
        )->name('businesses.toggle-status');
    });
