<?php

namespace App\Actions\Staff;

use App\Enums\StaffRole;
use App\Models\AuditEvent;
use App\Models\StaffInvitation;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use LogicException;
use SensitiveParameter;
use SensitiveParameterValue;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Throwable;

readonly class AcceptStaffInvitation
{
    public function __construct(
        private ValidateStaffInvitation $validateInvitation,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(
        int $invitationId,
        #[SensitiveParameter] string $token,
        string $name,
        #[SensitiveParameter] string $password,
        #[SensitiveParameter] string $passwordConfirmation,
    ): User {
        if (Auth::guard('web')->check()) {
            throw ValidationException::withMessages([
                'invitation' => 'Sign out before accepting an invitation.',
            ]);
        }

        if (
            $invitationId < 1
            || preg_match('/\A[0-9a-f]{64}\z/', $token) !== 1
        ) {
            throw $this->unavailable();
        }

        $name = trim($name);

        Validator::make(
            [
                'name' => $name,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'password' => [
                    'required',
                    'string',
                    Password::min(12)->mixedCase()->numbers()->symbols(),
                    'confirmed',
                ],
            ],
        )->validate();

        $connection = (new StaffInvitation)->getConnection();

        if ($connection->getDriverName() !== 'pgsql') {
            throw new LogicException(
                'Invitation acceptance currently requires PostgreSQL.',
            );
        }

        if ($connection->transactionLevel() !== 0) {
            throw new LogicException(
                'Invitation acceptance must own its transaction boundary.',
            );
        }

        $passwordHash = Hash::make($password);
        $secret = new SensitiveParameterValue($token);

        try {
            return $connection->transaction(function () use (
                $connection,
                $invitationId,
                $name,
                $passwordHash,
                $secret,
            ): User {
                $connection->statement(
                    'SET TRANSACTION ISOLATION LEVEL READ COMMITTED',
                );

                $lock = $connection->selectOne(
                    'SELECT CASE WHEN pg_try_advisory_xact_lock(?, ?) THEN 1 ELSE 0 END AS acquired',
                    [71001, 1],
                );

                if (! $lock || (int) $lock->acquired !== 1) {
                    throw new ConflictHttpException(
                        'Another staff change is running. Please retry.',
                    );
                }

                $candidate = StaffInvitation::on($connection->getName())
                    ->whereKey($invitationId)
                    ->first();

                if ($candidate === null) {
                    throw $this->unavailable();
                }

                $inviter = User::on($connection->getName())
                    ->whereKey($candidate->invited_by)
                    ->lockForUpdate()
                    ->first();

                if ($inviter === null) {
                    throw $this->unavailable();
                }

                $invitation = StaffInvitation::on($connection->getName())
                    ->whereKey($invitationId)
                    ->lockForUpdate()
                    ->first();

                if ($invitation === null) {
                    throw $this->unavailable();
                }

                $acceptedAt = CarbonImmutable::now('UTC');

                $roles = $this->validateInvitation->execute(
                    $invitation,
                    (string) $secret->getValue(),
                    $inviter,
                    $acceptedAt,
                );

                $existingUser = $connection->table('users')
                    ->whereRaw(
                        'LOWER(BTRIM(email)) = ?',
                        [$invitation->email],
                    )
                    ->exists();

                if ($existingUser) {
                    throw $this->unavailable();
                }

                $user = new User;
                $user->setConnection($connection->getName());

                $user->forceFill([
                    'name' => $name,
                    'email' => $invitation->email,
                    'password' => $passwordHash,
                    'is_active' => true,
                    'email_verified_at' => $acceptedAt,
                ])->save();

                foreach ($roles as $role) {
                    $user->assignRole($role);
                }

                $invitation->forceFill([
                    'accepted_at' => $acceptedAt,
                    'accepted_user_id' => $user->id,
                ])->save();

                AuditEvent::on($connection->getName())->create([
                    'event' => 'staff.invitation_accepted',
                    'actor_type' => 'user',
                    'actor_id' => $user->id,
                    'subject_id' => $user->id,
                    'occurred_at' => $acceptedAt,
                    'metadata' => [
                        'invitation_id' => $invitation->id,
                        'invited_by' => $inviter->id,
                        'roles' => array_map(
                            static fn (StaffRole $role): string => $role->value,
                            $roles,
                        ),
                    ],
                ]);

                return $user;
            });
        } catch (UniqueConstraintViolationException) {
            throw $this->unavailable();
        }
    }

    private function unavailable(): ValidationException
    {
        return ValidationException::withMessages([
            'invitation' => ValidateStaffInvitation::UNAVAILABLE,
        ]);
    }
}
