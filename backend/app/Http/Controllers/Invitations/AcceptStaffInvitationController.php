<?php

namespace App\Http\Controllers\Invitations;

use App\Actions\Staff\AcceptStaffInvitation;
use App\Actions\Staff\InspectStaffInvitation;
use App\Enums\StaffRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invitations\AcceptStaffInvitationRequest;
use App\Http\Requests\Invitations\InspectStaffInvitationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AcceptStaffInvitationController extends Controller
{
    public function show(Request $request, int $invitation): Response
    {
        $response = Inertia::render('auth/AcceptInvitation', [
            'invitationId' => $invitation,
            'mustSignOut' => $request->user() !== null,
        ])->toResponse($request);

        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Referrer-Policy', 'no-referrer');

        return $response;
    }

    public function inspect(
        InspectStaffInvitationRequest $request,
        int $invitation,
        InspectStaffInvitation $action,
    ): JsonResponse {
        $record = $action->execute(
            $invitation,
            (string) $request->validated('invitation_token'),
        );

        return response()->json([
            'id' => $record->id,
            'email' => $record->email,
            'expires_at' => $record->expires_at->utc()->toIso8601String(),
            'roles' => array_map(
                static fn (StaffRole $role): array => [
                    'value' => $role->value,
                    'label' => $role->name,
                ],
                $record->assignedRoles(),
            ),
        ])->header('Cache-Control', 'no-store, private');
    }

    /**
     * @throws Throwable
     */
    public function store(
        AcceptStaffInvitationRequest $request,
        int $invitation,
        AcceptStaffInvitation $action,
    ): RedirectResponse {
        $action->execute(
            $invitation,
            (string) $request->validated('invitation_token'),
            (string) $request->validated('name'),
            (string) $request->validated('password'),
            (string) $request->validated('password_confirmation'),
        );

        return to_route('login')->with(
            'status',
            'Your FieldOps account is ready. Please log in.',
        );
    }
}
