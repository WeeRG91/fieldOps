<?php

namespace App\Models;

use App\Enums\StaffRole;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * @property int $id
 * @property string $email
 * @property string $token_hash
 * @property int $invited_by
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $accepted_at
 * @property int|null $accepted_user_id
 * @property CarbonImmutable|null $revoked_at
 * @property int|null $revoked_by
 * @property CarbonImmutable|null $superseded_at
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
#[Hidden(['token_hash'])]
class StaffInvitation extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'immutable_datetime',
            'accepted_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
            'superseded_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    public function status(?DateTimeInterface $at = null): string
    {
        if (! $this->exists) {
            throw new LogicException(
                'Invitation status requires a persisted invitation.',
            );
        }

        if ($this->accepted_at !== null) {
            return 'accepted';
        }

        if ($this->revoked_at !== null) {
            return 'revoked';
        }

        if ($this->superseded_at !== null) {
            return 'replaced';
        }

        return $this->expires_at->lessThanOrEqualTo($at ?? now())
            ? 'expired'
            : 'pending';
    }

    public function isUsable(?DateTimeInterface $at = null): bool
    {
        return $this->exists && $this->status($at) === 'pending';
    }

    /**
     * @return list<StaffRole>
     */
    public function assignedRoles(): array
    {
        if (! $this->exists) {
            return [];
        }

        $values = $this->getConnection()
            ->table('staff_invitation_roles')
            ->where('invitation_id', $this->id)
            ->orderBy('role')
            ->pluck('role')
            ->all();

        return array_values(array_map(
            static fn (mixed $value): StaffRole => StaffRole::from((string) $value),
            $values,
        ));
    }
}
