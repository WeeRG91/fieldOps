<?php

use App\Http\Controllers\Invitations\AcceptStaffInvitationController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Staff\StaffInvitationController;
use App\Http\Middleware\RequireInvitationPasswordConfirmation;
use App\Http\Middleware\RequireStaffPasswordConfirmation;
use App\Models\StaffInvitation;
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

Route::middleware(['auth', 'staff', 'verified'])
    ->prefix('staff/invitations')
    ->name('staff.invitations.')
    ->controller(StaffInvitationController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index')
            ->can('viewAny', StaffInvitation::class);

        Route::post('/', 'store')
            ->name('store')
            ->middleware(
                RequireInvitationPasswordConfirmation::class.':create',
            );

        Route::post('/{invitation}/resend', 'resend')
            ->whereNumber('invitation')
            ->name('resend')
            ->middleware(
                RequireInvitationPasswordConfirmation::class.':resend',
            );

        Route::post('/{invitation}/revoke', 'revoke')
            ->whereNumber('invitation')
            ->name('revoke')
            ->middleware(
                RequireInvitationPasswordConfirmation::class.':revoke',
            );
    });

Route::prefix('invitations')
    ->name('invitations.accept.')
    ->controller(AcceptStaffInvitationController::class)
    ->group(function () {
        Route::get('/{invitation}/accept', 'show')
            ->whereNumber('invitation')
            ->name('show')
            ->middleware('throttle:30,1');

        Route::post('/{invitation}/inspect', 'inspect')
            ->whereNumber('invitation')
            ->name('inspect')
            ->middleware('throttle:invitation-preview');

        Route::post('/{invitation}/accept', 'store')
            ->whereNumber('invitation')
            ->name('store')
            ->middleware('throttle:invitation-accept');
    });

require __DIR__.'/settings.php';
