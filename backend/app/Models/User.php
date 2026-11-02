<?php

namespace App\Models;

use App\Enums\StaffRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use LogicException;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property bool $is_active
 * @property Carbon|null $deactivated_at
 * @property int|null $deactivated_by
 * @property string|null $deactivation_reason
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden([
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token',
    'deactivated_at',
    'deactivated_by',
    'deactivation_reason',
])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => false,
    ];

    public function hasRole(StaffRole $role): bool
    {
        if (! $this->exists) {
            return false;
        }

        return $this->getConnection()
            ->table('user_roles')
            ->where('user_id', $this->getKey())
            ->where('role', $role->value)
            ->exists();
    }

    public function assignRole(StaffRole $role): void
    {
        if (! $this->exists) {
            throw new LogicException(
                'Staff roles can only be assigned to persisted users.',
            );
        }

        $this->getConnection()
            ->table('user_roles')
            ->insert([
                'user_id' => $this->getKey(),
                'role' => $role->value,
            ]);
    }

    public function hasStaffRole(): bool
    {
        if (! $this->exists) {
            return false;
        }

        return $this->getConnection()
            ->table('user_roles')
            ->where('user_id', $this->getKey())
            ->whereIn(
                'role',
                array_map(
                    static fn (StaffRole $role): string => $role->value,
                    StaffRole::cases(),
                ),
            )
            ->exists();
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
        ];
    }
}
