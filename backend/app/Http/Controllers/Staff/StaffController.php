<?php

namespace App\Http\Controllers\Staff;

use App\Actions\Staff\ManageStaffAccess;
use App\Enums\StaffRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ActivateStaffRequest;
use App\Http\Requests\Staff\DeactivateStaffRequest;
use App\Http\Requests\Staff\ReplaceStaffRolesRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class StaffController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', User::class);

        $staff = User::query()
            ->select([
                'id',
                'name',
                'email',
                'email_verified_at',
                'is_active',
            ])
            ->orderBy('id')
            ->paginate(20);

        $roleRows = (new User)->getConnection()
            ->table('user_roles')
            ->whereIn('user_id', $staff->getCollection()->modelKeys())
            ->orderBy('role')
            ->get(['user_id', 'role']);

        /** @var array<int, list<string>> $rolesByUser */
        $rolesByUser = [];

        foreach ($roleRows as $row) {
            $rolesByUser[(int) $row->user_id][] = (string) $row->role;
        }

        return Inertia::render('staff/Index', [
            'staff' => $staff->through(
                fn (User $user): array => $this->presentStaff(
                    $user,
                    $rolesByUser[$user->id] ?? [],
                ),
            ),
        ]);
    }

    public function show(Request $request, User $staff): Response
    {
        Gate::authorize('view', $staff);

        $actor = $request->user();

        abort_unless($actor instanceof User, 401);

        $roles = $staff->getConnection()
            ->table('user_roles')
            ->where('user_id', $staff->id)
            ->orderBy('role')
            ->pluck('role')
            ->all();

        return Inertia::render('staff/Show', [
            'staff' => [
                ...$this->presentStaff($staff, $roles),
                'deactivated_at' => $staff->deactivated_at?->utc()->toIso8601String(),
                'deactivated_by' => $staff->deactivated_by,
                'deactivation_reason' => $staff->deactivation_reason,
            ],
            'roleOptions' => array_map(
                static fn (StaffRole $role): array => [
                    'value' => $role->value,
                    'label' => $role->name,
                ],
                StaffRole::cases(),
            ),
            'permissions' => [
                'updateRoles' => $actor->can('updateRoles', $staff),
                'activate' => ! $staff->isActive()
                    && $actor->can('activate', $staff),
                'deactivate' => $staff->isActive()
                    && $actor->can('deactivate', $staff),
            ],
        ]);
    }

    /**
     * @throws Throwable
     */
    public function updateRoles(
        ReplaceStaffRolesRequest $request,
        User $staff,
        ManageStaffAccess $action,
    ): RedirectResponse {
        /** @var list<string> $roles */
        $roles = $request->validated('roles');

        $action->replaceRoles($this->actor($request), $staff, $roles);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Staff roles saved',
        ]);

        return to_route('staff.show', ['staff' => $staff]);
    }

    /**
     * @throws Throwable
     */
    public function activate(
        ActivateStaffRequest $request,
        User $staff,
        ManageStaffAccess $action,
    ): RedirectResponse {
        /** @var list<string> $roles */
        $roles = $request->validated('roles');

        $action->activate($this->actor($request), $staff, $roles);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Staff account activated.',
        ]);

        return to_route('staff.show', ['staff' => $staff]);
    }

    /**
     * @throws Throwable
     */
    public function deactivate(
        DeactivateStaffRequest $request,
        User $staff,
        ManageStaffAccess $action,
    ): RedirectResponse {
        $action->deactivate(
            $this->actor($request),
            $staff,
            (string) $request->validated('reason'),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Staff account deactivated.',
        ]);

        return to_route('staff.show', ['staff' => $staff]);
    }

    private function actor(Request $request): User
    {
        $actor = $request->user();

        abort_unless($actor instanceof User, 401);

        return $actor;
    }

    /**
     * @param  array<int, mixed>  $roles
     * @return array<string, mixed>
     */
    private function presentStaff(User $staff, array $roles): array
    {
        return [
            'id' => $staff->id,
            'name' => $staff->name,
            'email' => $staff->email,
            'is_active' => $staff->isActive(),
            'email_verified' => $staff->hasVerifiedEmail(),
            'roles' => $roles,
        ];
    }
}
