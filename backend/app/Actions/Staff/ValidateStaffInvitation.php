<?php

namespace App\Actions\Staff;

use App\Enums\StaffRole;
use App\Models\StaffInvitation;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class ValidateStaffInvitation
{
    public const string UNAVAILABLE = 'This invitation is invalid or no longer available.';

    /**
     * @return list<StaffRole>
     */
    public function execute(
        StaffInvitation $invitation,
        #[SensitiveParameter] string $token,
        User $inviter,
        ?DateTimeInterface $at = null,
    ): array {
        if (
            ! $invitation->exists
            || ! $inviter->exists
            || $invitation->invited_by !== $inviter->id
            || $invitation->getConnection()->getName()
            !== $inviter->getConnection()->getName()
            || preg_match('/\A[0-9a-f]{64}\z/', $token) !== 1
            || ! hash_equals(
                $invitation->token_hash,
                hash('sha256', $token),
            )
            || ! $invitation->isUsable($at)
            || ! Gate::forUser($inviter)->allows(
                'create',
                StaffInvitation::class,
            )
        ) {
            throw ValidationException::withMessages([
                'invitation' => self::UNAVAILABLE,
            ]);
        }

        $roles = $invitation->assignedRoles();

        if ($roles === []) {
            throw ValidationException::withMessages([
                'invitation' => self::UNAVAILABLE,
            ]);
        }

        return $roles;
    }
}
