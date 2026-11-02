<?php

namespace App\Actions\Staff;

use App\Enums\StaffRole;
use App\Models\AuditEvent;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use LogicException;
use Throwable;

class BootstrapAdministrator
{
    /**
     * @throws Throwable
     */
    public function execute(
        string $email,
        ?int $existingUserId,
        ?string $name = null,
        ?string $password = null,
    ): User {
        $email = Str::lower(trim($email));

        Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email', 'max:255']],
        )->validate();

        if ($existingUserId === null) {
            Validator::make(
                ['name' => $name, 'password' => $password],
                [
                    'name' => ['required', 'string', 'max:255'],
                    'password' => [
                        'required',
                        'string',
                        Password::min(12)->mixedCase()->numbers()->symbols(),
                    ],
                ]
            )->validate();
        } elseif ($name !== null || $password !== null) {
            throw new DomainException(
                'Existing-account bootstrap cannot replace identity or password.',
            );
        }

        $connection = (new User)->getConnection();

        if ($connection->getDriverName() !== 'pgsql') {
            throw new LogicException(
                'Administrator bootstrap currently requires PostgreSQL.',
            );
        }

        return $connection->transaction(function () use (
            $connection,
            $email,
            $existingUserId,
            $name,
            $password,
        ): User {
            $lock = $connection->selectOne(
                'SELECT CASE WHEN pg_try_advisory_xact_lock(?, ?) THEN 1 ELSE 0 END AS acquired',
                [71001, 1],
            );

            if (! $lock || (int) $lock->acquired !== 1) {
                throw new DomainException(
                    'Another administrator bootstrap is running.',
                );
            }

            if (
                $connection->table('user_roles')
                    ->where('role', StaffRole::Administrator->value)
                    ->exists()
            ) {
                throw new DomainException(
                    'An Administrator assignment already exists. Use recovery instead.',
                );
            }

            $matches = User::on($connection->getName())
                ->whereRaw('LOWER(email) = ?', [$email])
                ->lockForUpdate()
                ->get();

            if ($matches->count() > 1) {
                throw new DomainException(
                    'Ambiguous account email. Resolve the collision before bootstrap.',
                );
            }

            $user = $matches->first();

            if ($existingUserId !== null) {
                if (! $user || $user->id !== $existingUserId) {
                    throw new DomainException(
                        'The selected account changed. Restart bootstrap.',
                    );
                }

                if (
                    $user->deactivated_at !== null
                    || $user->deactivated_by !== null
                    || $user->deactivation_reason !== null
                ) {
                    throw new DomainException(
                        'A previously deactivated account requires recovery.',
                    );
                }
            } else {
                if ($user) {
                    throw new DomainException(
                        'An account now exists for this email. Restart bootstrap.',
                    );
                }

                $user = User::on($connection->getName())->create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ]);
            }

            $wasActive = $user->isActive();

            $user->forceFill(['is_active' => true])->save();
            $user->assignRole(StaffRole::Administrator);

            AuditEvent::on($connection->getName())->create([
                'event' => 'staff.administrator_bootstrapped',
                'actor_type' => 'console',
                'actor_id' => null,
                'subject_id' => $user->id,
                'occurred_at' => now(),
                'metadata' => [
                    'account_source' => $existingUserId === null
                        ? 'created'
                        : 'promoted',
                    'assigned_roles' => [StaffRole::Administrator->value],
                    'activation' => [
                        'before' => $wasActive,
                        'after' => true,
                    ],
                ],
            ]);

            return $user;
        });
    }
}
