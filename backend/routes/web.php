<?php

use App\Http\Controllers\Staff\StaffController;
use App\Http\Middleware\RequireStaffPasswordConfirmation;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'staff', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::middleware(['auth', 'staff', 'verified'])
    ->prefix('staff')
    ->name('staff.')
    ->controller(StaffController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index')
            ->can('viewAny', User::class);

        Route::get('/{staff}', 'show')
            ->whereNumber('staff')
            ->name('show')
            ->can('view', 'staff');

        Route::patch('/{staff}/roles', 'updateRoles')
            ->whereNumber('staff')
            ->name('roles.update')
            ->middleware(
                RequireStaffPasswordConfirmation::class.':updateRoles',
            );

        Route::post('/{staff}/activate', 'activate')
            ->whereNumber('staff')
            ->name('activate')
            ->middleware(
                RequireStaffPasswordConfirmation::class.':activate',
            );

        Route::post('/{staff}/deactivate', 'deactivate')
            ->whereNumber('staff')
            ->name('deactivate')
            ->middleware(
                RequireStaffPasswordConfirmation::class.':deactivate',
            );
    });

require __DIR__.'/settings.php';
