<?php

namespace App\Policies;

use App\Enums\StaffRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $this->canManageStaff($actor);
    }

    public function view(User $actor, User $subject): bool
    {
        return $this->canManageStaff($actor);
    }

    public function updateRoles(User $actor, User $subject): bool
    {
        return $this->canManageStaff($actor)
            && $actor->isNot($subject);
    }

    public function activate(User $actor, User $subject): bool
    {
        return $this->canManageStaff($actor)
            && $actor->isNot($subject);
    }

    public function deactivate(User $actor, User $subject): bool
    {
        return $this->canManageStaff($actor)
            && $actor->isNot($subject);
    }

    private function canManageStaff(User $actor): bool
    {
        return $actor->isActive()
            && $actor->hasVerifiedEmail()
            && $actor->hasRole(StaffRole::Administrator);
    }
}
