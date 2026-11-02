<?php

namespace App\Actions\Staff;

use App\Enums\StaffRole;
use App\Models\AuditEvent;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use LogicException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Throwable;

class ManageStaffAccess
{
    /**
     * @param  list<string>  $roles
     *
     * @throws Throwable
     */
    public function replaceRoles(
        User $actor,
        User $subject,
        array $roles,
    ): User {
        Validator::make(
            ['roles' => $roles],
            [
                'roles' => ['required', 'array', 'list', 'min:1'],
                'roles.*' => [
                    'required',
                    'string',
                    'distinct:strict',
                    Rule::enum(StaffRole::class),
                ],
            ],
        )->validate();

        sort($roles, SORT_STRING);

        return $this->mutate(
            $actor,
            $subject,
            'updateRoles',
            function (User $lockedActor, User $lockedSubject) use ($roles): void {
                $connection = $lockedSubject->getConnection();

                $before = $connection->table('user_roles')
                    ->where('user_id', $lockedSubject->id)
                    ->orderBy('role')
                    ->pluck('role')
                    ->all();

                if ($before === $roles) {
                    return;
                }

                $connection->table('user_roles')
                    ->where('user_id', $lockedSubject->id)
                    ->delete();

                $connection->table('user_roles')->insert(
                    array_map(
                        static fn (string $role): array => [
                            'user_id' => $lockedSubject->id,
                            'role' => $role,
                        ],
                        $roles,
                    ),
                );

                $this->revokeBrowserAccess($lockedSubject);

                AuditEvent::on($connection->getName())->create([
                    'event' => 'staff.roles_replaced',
                    'actor_type' => 'user',
                    'actor_id' => $lockedActor->id,
                    'subject_id' => $lockedSubject->id,
                    'occurred_at' => now(),
                    'metadata' => [
                        'roles' => [
                            'before' => $before,
                            'after' => $roles,
                            'granted' => array_values(array_diff($roles, $before)),
                            'revoked' => array_values(array_diff($before, $roles)),
                        ],
                    ],
                ]);
            }
        );
    }

    /**
     * @throws Throwable
     */
    public function deactivate(
        User $actor,
        User $subject,
        string $reason,
    ): User {
        $reason = trim($reason);

        Validator::make(
            ['reason' => $reason],
            ['reason' => ['required', 'string', 'max:1000']],
        )->validate();

        return $this->mutate(
            $actor,
            $subject,
            'deactivate',
            function (User $lockedActor, User $lockedSubject) use ($reason): void {
                if (! $lockedSubject->isActive()) {
                    throw ValidationException::withMessages([
                        'staff' => 'This account is already inactive.',
                    ]);
                }

                $lockedSubject->forceFill([
                    'is_active' => false,
                    'deactivated_at' => now(),
                    'deactivated_by' => $lockedActor->id,
                    'deactivation_reason' => $reason,
                ])->save();

                $this->revokeBrowserAccess($lockedSubject);

                AuditEvent::on($lockedSubject->getConnection()->getName())->create([
                    'event' => 'staff.deactivated',
                    'actor_type' => 'user',
                    'actor_id' => $lockedActor->id,
                    'subject_id' => $lockedSubject->id,
                    'occurred_at' => now(),
                    'metadata' => [
                        'activation' => [
                            'before' => true,
                            'after' => false,
                        ],
                        'reason' => $reason,
                    ],
                ]);
            },
        );
    }

    /**
     * @param  list<string>  $roles
     *
     * @throws Throwable
     */
    public function activate(
        User $actor,
        User $subject,
        array $roles,
    ): User {
        Validator::make(
            ['roles' => $roles],
            [
                'roles' => ['required', 'array', 'list', 'min:1'],
                'roles.*' => [
                    'required',
                    'string',
                    'distinct:strict',
                    Rule::enum(StaffRole::class),
                ],
            ],
        )->validate();

        sort($roles, SORT_STRING);

        return $this->mutate(
            $actor,
            $subject,
            'activate',
            function (User $lockedActor, User $lockedSubject) use ($roles): void {
                if ($lockedSubject->isActive()) {
                    throw ValidationException::withMessages([
                        'staff' => 'This account is already active.',
                    ]);
                }

                $connection = $lockedSubject->getConnection();

                $beforeRoles = $connection->table('user_roles')
                    ->where('user_id', $lockedSubject->id)
                    ->orderBy('role')
                    ->pluck('role')
                    ->all();

                $connection->table('user_roles')
                    ->where('user_id', $lockedSubject->id)
                    ->delete();

                $connection->table('user_roles')->insert(
                    array_map(
                        static fn (string $role): array => [
                            'user_id' => $lockedSubject->id,
                            'role' => $role,
                        ],
                        $roles,
                    ),
                );

                $lockedSubject->forceFill([
                    'is_active' => true,
                    'deactivated_at' => null,
                    'deactivated_by' => null,
                    'deactivation_reason' => null,
                ])->save();

                $this->revokeBrowserAccess($lockedSubject);

                AuditEvent::on($connection->getName())->create([
                    'event' => 'staff.activated',
                    'actor_type' => 'user',
                    'actor_id' => $lockedActor->id,
                    'subject_id' => $lockedSubject->id,
                    'occurred_at' => now(),
                    'metadata' => [
                        'activation' => [
                            'before' => false,
                            'after' => true,
                        ],
                        'roles' => [
                            'before' => $beforeRoles,
                            'after' => $roles,
                            'granted' => array_values(
                                array_diff($roles, $beforeRoles),
                            ),
                            'revoked' => array_values(
                                array_diff($beforeRoles, $roles),
                            ),
                        ],
                    ],
                ]);
            }
        );
    }

    /**
     * @param  Closure(User, User): void  $operation
     *
     * @throws Throwable
     */
    private function mutate(
        User $actor,
        User $subject,
        string $ability,
        Closure $operation,
    ): User {
        $connection = (new User)->getConnection();

        if (
            ! $actor->exists
            || ! $subject->exists
            || $actor->getConnection()->getName() !== $connection->getName()
            || $subject->getConnection()->getName() !== $connection->getName()
        ) {
            throw new LogicException(
                'Staff mutations require persisted users on the application connection.',
            );
        }

        if ($connection->getDriverName() !== 'pgsql') {
            throw new LogicException(
                'Staff mutations currently require PostgreSQL.',
            );
        }

        if ($connection->transactionLevel() !== 0) {
            throw new LogicException(
                'Staff mutations must own their transaction boundary.',
            );
        }

        return $connection->transaction(function () use (
            $connection,
            $actor,
            $subject,
            $ability,
            $operation,
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

            $users = User::on($connection->getName())
                ->whereKey([$actor->id, $subject->id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $lockedActor = $users->get($actor->id);
            $lockedSubject = $users->get($subject->id);

            if (
                ! $lockedActor instanceof User
                || ! $lockedSubject instanceof User
            ) {
                throw (new ModelNotFoundException)->setModel(User::class);
            }

            Gate::forUser($lockedActor)->authorize(
                $ability,
                $lockedSubject,
            );

            $operation($lockedActor, $lockedSubject);

            $hasUsableAdministrator = $connection->table('users')
                ->join('user_roles', 'user_roles.user_id', '=', 'users.id')
                ->where('users.is_active', true)
                ->whereNotNull('users.email_verified_at')
                ->where('user_roles.role', StaffRole::Administrator->value)
                ->exists();

            if (! $hasUsableAdministrator) {
                throw ValidationException::withMessages([
                    'staff' => 'At least one active, verified Administrator must remain.',
                ]);
            }

            return $lockedSubject;
        });
    }

    private function revokeBrowserAccess(User $subject): void
    {
        if (config('session.driver') !== 'database') {
            throw new LogicException(
                'Staff revocation currently requires database sessions.',
            );
        }

        $connection = $subject->getConnection();
        $sessionConnection = DB::connection(config('session.connection'));

        if ($sessionConnection->getName() !== $connection->getName()) {
            throw new LogicException(
                'Staff revocation requires sessions on the application connection.',
            );
        }

        $subject->forceFill([
            'remember_token' => Str::random(60),
        ])->save();

        $connection->table((string) config('session.table', 'sessions'))
            ->where('user_id', $subject->id)
            ->delete();
    }
}
