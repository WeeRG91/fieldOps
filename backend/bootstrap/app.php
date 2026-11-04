<?php

use App\Http\Middleware\EnsureStaffAccess;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            EnsureStaffAccess::class.':optional',
        ]);

        $middleware->alias([
            'staff' => EnsureStaffAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['invitation_token']);

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (
            ConflictHttpException $exception,
            Request $request
        ): ?RedirectResponse {
            if (
                $request->header('X-Inertia')
                && $request->routeIs(
                    'staff.invitations.store',
                    'staff.invitations.resend',
                    'staff.invitations.revoke',
                )
            ) {
                return to_route(
                    'staff.invitations.index',
                    [],
                    303,
                )->withErrors([
                    'invitation' => 'Another staff change is running. Please retry.',
                ]);
            }

            if (
                $request->header('X-Inertia')
                && $request->routeIs('invitations.accept.store')
            ) {
                return to_route(
                    'invitations.accept.show',
                    ['invitation' => $request->route('invitation')],
                    303,
                )->withErrors([
                    'invitation' => 'Another staff change is running. Please retry.',
                ]);
            }

            if (
                ! $request->header('X-Inertia')
                || ! $request->routeIs(
                    'staff.roles.update',
                    'staff.activate',
                    'staff.deactivate',
                )
            ) {
                return null;
            }

            return to_route(
                'staff.show',
                ['staff' => $request->route('staff')],
                303,
            )->withErrors([
                'staff' => 'Another staff change is running. Please retry.',
            ]);
        });

        $exceptions->render(function (
            TooManyRequestsHttpException $exception,
            Request $request,
        ): ?RedirectResponse {
            if (
                ! $request->header('X-Inertia')
                || ! $request->routeIs('invitations.accept.store')
            ) {
                return null;
            }

            return to_route(
                'invitations.accept.show',
                ['invitation' => $request->route('invitation')],
                303,
            )->withErrors([
                'invitation' => 'Too many attempts. Please wait before trying again.',
            ]);
        });
    })->create();
