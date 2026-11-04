<?php

namespace App\Http\Middleware;

use App\Models\StaffInvitation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RequireInvitationPasswordConfirmation
{
    public function handle(
        Request $request,
        Closure $next,
        string $ability,
    ): Response {
        if ($ability === 'create') {
            Gate::authorize('create', StaffInvitation::class);
        } else {
            $invitation = $request->route('invitation');

            abort_unless($invitation instanceof StaffInvitation, 404);

            Gate::authorize($ability, $invitation);
        }

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
            route('staff.invitations.index'),
        );

        return redirect()->route('password.confirm');
    }
}
