<?php

namespace App\Http\Controllers\Staff;

use App\Actions\Staff\ManageStaffInvitations;
use App\Actions\Staff\SendStaffInvitation;
use App\Enums\StaffRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\IssueStaffInvitationRequest;
use App\Http\Requests\Staff\ResendStaffInvitationRequest;
use App\Http\Requests\Staff\RevokeStaffInvitationRequest;
use App\Models\StaffInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Random\RandomException;
use Throwable;

class StaffInvitationController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', StaffInvitation::class);

        $actor = $this->actor($request);

        $invitations = StaffInvitation::query()
            ->select([
                'id',
                'email',
                'invited_by',
                'expires_at',
                'accepted_at',
                'revoked_at',
                'superseded_at',
                'created_at',
            ])
            ->orderByDesc('id')
            ->paginate(20);

        $roleRows = (new StaffInvitation)->getConnection()
            ->table('staff_invitation_roles')
            ->whereIn(
                'invitation_id',
                $invitations->getCollection()->modelKeys(),
            )
            ->orderBy('role')
            ->get(['invitation_id', 'role']);

        /** @var array<int, list<string>> $rolesByInvitation */
        $rolesByInvitation = [];

        foreach ($roleRows as $row) {
            $rolesByInvitation[(int) $row->invitation_id][] = (string) $row->role;
        }

        $now = now();

        return Inertia::render('staff/invitations/Index', [
            'invitations' => $invitations->through(
                function (StaffInvitation $invitation) use (
                    $actor,
                    $rolesByInvitation,
                    $now,
                ): array {
                    $status = $invitation->status($now);

                    $canChange = in_array(
                        $status,
                        ['pending', 'expired'],
                        true,
                    );

                    return [
                        'id' => $invitation->id,
                        'email' => $invitation->email,
                        'invited_by' => $invitation->invited_by,
                        'roles' => $rolesByInvitation[$invitation->id] ?? [],
                        'status' => $status,
                        'created_at' => $invitation->created_at->utc()->toIso8601String(),
                        'expires_at' => $invitation->expires_at->utc()->toIso8601String(),
                        'permissions' => [
                            'resend' => $canChange
                                && $actor->can('resend', $invitation),
                            'revoke' => $canChange
                                && $actor->can('revoke', $invitation),
                        ],
                    ];
                },
            ),
            'roleOptions' => array_map(
                static fn (StaffRole $role): array => [
                    'value' => $role->value,
                    'label' => $role->name,
                ],
                StaffRole::cases(),
            ),
            'permissions' => [
                'create' => $actor->can('create', StaffInvitation::class),
            ],
        ]);
    }

    /**
     * @throws Throwable
     * @throws RandomException
     */
    public function store(
        IssueStaffInvitationRequest $request,
        ManageStaffInvitations $action,
        SendStaffInvitation $sender,
    ): RedirectResponse {
        /** @var list<string> $roles */
        $roles = $request->validated('roles');

        $issued = $action->issue(
            $this->actor($request),
            (string) $request->validated('email'),
            $roles,
            $request->boolean('administrator_confirmation'),
        );

        $sent = $sender->execute($issued);

        Inertia::flash('toast', [
            'type' => $sent ? 'success' : 'warning',
            'message' => $sent
                ? 'Invitation created and submitted to the mail server.'
                : 'Invitation saved, but email delivery could not be confirmed. Use Resend to send a replacement link.',
        ]);

        return to_route('staff.invitations.index');
    }

    /**
     * @throws Throwable
     * @throws RandomException
     */
    public function resend(
        ResendStaffInvitationRequest $request,
        StaffInvitation $invitation,
        ManageStaffInvitations $action,
        SendStaffInvitation $sender,
    ): RedirectResponse {
        $issued = $action->resend(
            $this->actor($request),
            $invitation,
            $request->boolean('administrator_confirmation'),
        );

        $sent = $sender->execute($issued);

        Inertia::flash('toast', [
            'type' => $sent ? 'success' : 'warning',
            'message' => $sent
                ? 'Replacement invitation submitted to the mail server. The previous link is invalid.'
                : 'Replacement invitation saved, but email delivery could not be confirmed. The previous link is invalid; use Resend to try again.',
        ]);

        return to_route('staff.invitations.index');
    }

    /**
     * @throws Throwable
     */
    public function revoke(
        RevokeStaffInvitationRequest $request,
        StaffInvitation $invitation,
        ManageStaffInvitations $action,
    ): RedirectResponse {
        $action->revoke($this->actor($request), $invitation);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Invitation revoked. Its acceptance link is no longer valid.',
        ]);

        return to_route('staff.invitations.index');
    }

    private function actor(Request $request): User
    {
        $actor = $request->user();

        abort_unless($actor instanceof User, 401);

        return $actor;
    }
}
