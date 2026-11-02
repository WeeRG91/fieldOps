<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffAccess
{
    public function handle(
        Request $request,
        Closure $next,
        string $mode = 'required',
    ): Response {
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        $user = $request->user();

        if (
            ! $user
            && $request->routeIs('two-factor.login', 'two-factor.login.store')
            && $request->session()->has('login.id')
        ) {
            $candidate = User::query()->find($request->session()->get('login.id'));

            if (
                ! $candidate instanceof User
                || ! $candidate->isActive()
                || ! $candidate->hasStaffRole()
            ) {
                $request->session()->forget(['login.id', 'login.remember']);

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => __('auth.failed'),
                    ], 422);
                }

                return redirect()->route('login')->withErrors([
                    'email' => __('auth.failed'),
                ]);
            }
        }

        if (! $user instanceof User) {
            if ($mode === 'optional') {
                return $next($request);
            }

            abort(401);
        }

        if (! $user->isActive() || ! $user->hasStaffRole()) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, 'Staff access is unavailable');
        }

        if (
            $request->routeIs(
                'two-factor.enable',
                'two-factor.confirm',
                'two-factor.disable',
                'two-factor.qr-code',
                'two-factor.secret-key',
                'two-factor.recovery-codes',
                'two-factor.regenerate-recovery-codes',
                'passkey.confirm-options',
                'passkey.confirm',
                'passkey.registration-options',
                'passkey.store',
                'passkey.destroy',
                'user-profile-information.update',
                'user-password.update',
            )
            && ! $user->hasVerifiedEmail()
        ) {
            if ($request->expectsJson()) {
                abort(403, 'Email verification is required.');
            }

            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
