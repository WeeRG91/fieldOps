<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RequireStaffPasswordConfirmation
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next,
        string $ability,
    ): Response {
        $subject = $request->route('staff');

        abort_unless($subject instanceof User, 404);

        Gate::authorize($ability, $subject);

        $confirmedAt = (int) $request->session()->get(
            'auth.password_confirmed_at',
            0,
        );

        $timeout = (int) config('auth.password_timeout', 10800);

        if (
            $confirmedAt > 0
            && now()->getTimestamp() - $confirmedAt <= $timeout
        ) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Password confirmation required.',
            ], 423);
        }

        $request->session()->put(
            'url.intended',
            route('staff.show', ['staff' => $subject]),
        );

        return redirect()->route('password.confirm');
    }
}
