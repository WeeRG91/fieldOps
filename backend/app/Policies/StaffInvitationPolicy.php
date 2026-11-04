<?php

namespace App\Policies;

use App\Enums\StaffRole;
use App\Models\StaffInvitation;
use App\Models\User;

class StaffInvitationPolicy
{
    public function viewAny(User $actor): bool
    {
        return $this->canManageInvitations($actor);
    }

    public function view(User $actor, StaffInvitation $invitation): bool
    {
        return $this->canManageInvitations($actor);
    }

    public function create(User $actor): bool
    {
        return $this->canManageInvitations($actor);
    }

    public function resend(User $actor, StaffInvitation $invitation): bool
    {
        return $this->canManageInvitations($actor);
    }

    public function revoke(User $actor, StaffInvitation $invitation): bool
    {
        return $this->canManageInvitations($actor);
    }

    private function canManageInvitations(User $actor): bool
    {
        return $actor->isActive()
            && $actor->hasVerifiedEmail()
            && $actor->hasRole(StaffRole::Administrator);
    }
}
