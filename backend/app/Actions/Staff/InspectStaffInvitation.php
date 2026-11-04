<?php

namespace App\Actions\Staff;

use App\Models\StaffInvitation;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

readonly class InspectStaffInvitation
{
    public function __construct(
        private ValidateStaffInvitation $validateInvitation,
    ) {}

    public function execute(
        int $invitationId,
        #[SensitiveParameter] string $token,
    ): StaffInvitation {
        $invitation = StaffInvitation::query()
            ->whereKey($invitationId)
            ->first();

        if ($invitation === null) {
            throw ValidationException::withMessages([
                'invitation' => ValidateStaffInvitation::UNAVAILABLE,
            ]);
        }

        $inviter = User::on($invitation->getConnection()->getName())
            ->whereKey($invitation->invited_by)
            ->first();

        if ($inviter === null) {
            throw ValidationException::withMessages([
                'invitation' => ValidateStaffInvitation::UNAVAILABLE,
            ]);
        }

        $this->validateInvitation->execute(
            $invitation,
            $token,
            $inviter,
        );

        return $invitation;
    }
}
