<?php

namespace App\Actions\Staff;

use App\Data\Staff\IssuedStaffInvitation;
use App\Enums\StaffRole;
use App\Models\AuditEvent;
use App\Models\StaffInvitation;
use App\Models\User;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use LogicException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Throwable;

class ManageStaffInvitations
{
    /**
     * @param  list<string>  $roles
     *
     * @throws RandomException
     * @throws Throwable
     */
    public function issue(
        User $actor,
        string $email,
        array $roles,
        bool $administratorConfirmed = false,
    ): IssuedStaffInvitation {
        $email = Str::lower(trim($email));

        Validator::make(
            ['email' => $email, 'roles' => $roles],
            [
                'email' => ['required', 'string', 'email', 'max:255'],
                'roles' => ['required', 'array', 'list', 'min:1'],
                'roles.*' => [
                    'required',
                    'string',
                    'distinct:strict',
                    Rule::enum(StaffRole::class),
                ],
            ],
        )->validate();

        $includesAdministrator = in_array(
            StaffRole::Administrator->value,
            $roles,
            true,
        );

        if ($includesAdministrator && ! $administratorConfirmed) {
            throw ValidationException::withMessages([
                'administrator_confirmation' => 'Explicitly confirm the Administrator role grant.',
            ]);
        }

        sort($roles, SORT_STRING);

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $created = $this->mutate(
            $actor,
            'create',
            null,
            function (
                User $lockedActor,
                ?StaffInvitation $_invitation,
            ) use (
                $email,
                $roles,
                $tokenHash,
                $includesAdministrator,
            ): StaffInvitation {
                $connection = $lockedActor->getConnection();
                $issuedAt = CarbonImmutable::now('UTC');

                $existingUser = $connection->table('users')
                    ->whereRaw('LOWER(BTRIM(email)) = ?', [$email])
                    ->exists();

                if ($existingUser) {
                    throw ValidationException::withMessages([
                        'email' => 'An account already exists for this email.',
                    ]);
                }

                $previous = StaffInvitation::on($connection->getName())
                    ->where('email', $email)
                    ->whereNull('accepted_at')
                    ->whereNull('revoked_at')
                    ->whereNull('superseded_at')
                    ->lockForUpdate()
                    ->first();

                if ($previous?->isUsable($issuedAt)) {
                    throw ValidationException::withMessages([
                        'email' => 'A pending invitation already exists. Use Resend.',
                    ]);
                }

                if ($previous !== null) {
                    $previous->forceFill([
                        'superseded_at' => $issuedAt,
                    ])->save();
                }

                $invitation = $this->createRecord(
                    $lockedActor,
                    $email,
                    $roles,
                    $tokenHash,
                    $issuedAt,
                );

                AuditEvent::on($connection->getName())->create([
                    'event' => 'staff.invitation_issued',
                    'actor_type' => 'user',
                    'actor_id' => $lockedActor->id,
                    'subject_id' => null,
                    'occurred_at' => $issuedAt,
                    'metadata' => [
                        'invitation_id' => $invitation->id,
                        'roles' => $roles,
                        'expires_at' => $invitation->expires_at->toIso8601String(),
                        'superseded_invitation_id' => $previous?->id,
                        'administrator_role_confirmed' => $includesAdministrator,
                    ],
                ]);

                return $invitation;
            }
        );

        return new IssuedStaffInvitation($created, $token);
    }

    /**
     * @throws Throwable
     * @throws RandomException
     */
    public function resend(
        User $actor,
        StaffInvitation $invitation,
        bool $administratorConfirmed = false,
    ): IssuedStaffInvitation {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $created = $this->mutate(
            $actor,
            'resend',
            $invitation,
            function (
                User $lockedActor,
                ?StaffInvitation $lockedInvitation,
            ) use (
                $tokenHash,
                $administratorConfirmed,
            ): StaffInvitation {
                if ($lockedInvitation === null) {
                    throw new LogicException(
                        'Resend requires an existing invitation.',
                    );
                }

                $issuedAt = CarbonImmutable::now('UTC');
                $previousStatus = $lockedInvitation->status($issuedAt);

                if (! in_array($previousStatus, ['pending', 'expired'], true)) {
                    throw ValidationException::withMessages([
                        'invitation' => 'This invitation can no longer be resent.',
                    ]);
                }

                $roles = array_map(
                    static fn (StaffRole $role): string => $role->value,
                    $lockedInvitation->assignedRoles(),
                );

                if ($roles === []) {
                    throw ValidationException::withMessages([
                        'invitation' => 'This invitation has no roles. Revoke it and issue a new invitation.',
                    ]);
                }

                $includesAdministrator = in_array(
                    StaffRole::Administrator->value,
                    $roles,
                    true,
                );

                if ($includesAdministrator && ! $administratorConfirmed) {
                    throw ValidationException::withMessages([
                        'administrator_confirmation' => 'Explicitly confirm the Administrator role grant.',
                    ]);
                }

                $connection = $lockedActor->getConnection();

                $existingUser = $connection->table('users')
                    ->whereRaw(
                        'LOWER(BTRIM(email)) = ?',
                        [$lockedInvitation->email],
                    )
                    ->exists();

                if ($existingUser) {
                    throw ValidationException::withMessages([
                        'invitation' => 'An account already exists for this email.',
                    ]);
                }

                $lockedInvitation->forceFill([
                    'superseded_at' => $issuedAt,
                ])->save();

                $replacement = $this->createRecord(
                    $lockedActor,
                    $lockedInvitation->email,
                    $roles,
                    $tokenHash,
                    $issuedAt,
                );

                AuditEvent::on($connection->getName())->create([
                    'event' => 'staff.invitation_resent',
                    'actor_type' => 'user',
                    'actor_id' => $lockedActor->id,
                    'subject_id' => null,
                    'occurred_at' => $issuedAt,
                    'metadata' => [
                        'invitation_id' => $replacement->id,
                        'superseded_invitation_id' => $lockedInvitation->id,
                        'previous_status' => $previousStatus,
                        'roles' => $roles,
                        'expires_at' => $replacement->expires_at->toIso8601String(),
                        'administrator_role_confirmed' => $includesAdministrator,
                    ],
                ]);

                return $replacement;
            }
        );

        return new IssuedStaffInvitation($created, $token);
    }

    /**
     * @throws Throwable
     */
    public function revoke(
        User $actor,
        StaffInvitation $invitation,
    ): StaffInvitation {
        return $this->mutate(
            $actor,
            'revoke',
            $invitation,
            function (
                User $lockedActor,
                ?StaffInvitation $lockedInvitation,
            ): StaffInvitation {
                if ($lockedInvitation === null) {
                    throw new LogicException(
                        'Revocation requires an existing invitation.',
                    );
                }

                $revokedAt = CarbonImmutable::now('UTC');
                $previousStatus = $lockedInvitation->status($revokedAt);

                if (! in_array($previousStatus, ['pending', 'expired'], true)) {
                    throw ValidationException::withMessages([
                        'invitation' => 'This invitation can no longer be revoked.',
                    ]);
                }

                $lockedInvitation->forceFill([
                    'revoked_at' => $revokedAt,
                    'revoked_by' => $lockedActor->id,
                ])->save();

                AuditEvent::on($lockedInvitation->getConnection()->getName())
                    ->create([
                        'event' => 'staff.invitation_revoked',
                        'actor_type' => 'user',
                        'actor_id' => $lockedActor->id,
                        'subject_id' => null,
                        'occurred_at' => $revokedAt,
                        'metadata' => [
                            'invitation_id' => $lockedInvitation->id,
                            'previous_status' => $previousStatus,
                        ],
                    ]);

                return $lockedInvitation;
            }
        );
    }

    /**
     * @param  list<string>  $roles
     */
    private function createRecord(
        User $actor,
        string $email,
        array $roles,
        string $tokenHash,
        CarbonImmutable $issuedAt,
    ): StaffInvitation {
        $connection = $actor->getConnection();

        $invitation = new StaffInvitation;
        $invitation->setConnection($connection->getName());

        $invitation->forceFill([
            'email' => $email,
            'token_hash' => $tokenHash,
            'invited_by' => $actor->id,
            'expires_at' => $issuedAt->addHours(48),
            'created_at' => $issuedAt,
            'updated_at' => $issuedAt,
        ])->save();

        $connection->table('staff_invitation_roles')->insert(
            array_map(
                static fn (string $role): array => [
                    'invitation_id' => $invitation->id,
                    'role' => $role,
                ],
                $roles,
            ),
        );

        return $invitation;
    }

    /**
     * @param  Closure(User, StaffInvitation|null): StaffInvitation  $operation
     *
     * @throws Throwable
     */
    private function mutate(
        User $actor,
        string $ability,
        ?StaffInvitation $invitation,
        Closure $operation,
    ): StaffInvitation {
        $connection = (new StaffInvitation)->getConnection();

        if (
            ! $actor->exists
            || $actor->getConnection()->getName() !== $connection->getName()
        ) {
            throw new LogicException(
                'Invitation management requires a persisted actor on the application connection.',
            );
        }

        if (
            $invitation !== null
            && (
                ! $invitation->exists
                || $invitation->getConnection()->getName() !== $connection->getName()
            )
        ) {
            throw new LogicException(
                'Invitation management requires a persisted invitation on the application connection.',
            );
        }

        if ($connection->getDriverName() !== 'pgsql') {
            throw new LogicException(
                'Invitation management currently requires PostgreSQL.',
            );
        }

        if ($connection->transactionLevel() !== 0) {
            throw new LogicException(
                'Invitation management must own its transaction boundary.',
            );
        }

        return $connection->transaction(function () use (
            $connection,
            $actor,
            $ability,
            $invitation,
            $operation,
        ): StaffInvitation {
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

            $lockedActor = User::on($connection->getName())
                ->whereKey($actor->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedInvitation = $invitation === null
                ? null
                : StaffInvitation::on($connection->getName())
                    ->whereKey($invitation->id)
                    ->lockForUpdate()
                    ->firstOrFail();

            Gate::forUser($lockedActor)->authorize(
                $ability,
                $lockedInvitation ?? StaffInvitation::class,
            );

            return $operation($lockedActor, $lockedInvitation);
        });
    }
}
