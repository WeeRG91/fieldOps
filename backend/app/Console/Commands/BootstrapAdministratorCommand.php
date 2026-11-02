<?php

namespace App\Console\Commands;

use App\Actions\Staff\BootstrapAdministrator;
use App\Enums\StaffRole;
use App\Models\User;
use DomainException;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

#[Signature('staff:bootstrap-admin')]
#[Description('Interactively provision the first staff Administrator')]
class BootstrapAdministratorCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(BootstrapAdministrator $bootstrap): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('This command requires interactive confirmation.');

            return self::FAILURE;
        }

        try {
            return $this->bootstrap($bootstrap);
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $messages) {
                foreach ($messages as $message) {
                    $this->error($message);
                }
            }

            Log::notice('staff.bootstrap.refused', ['reason' => 'validation']);

            return self::FAILURE;
        } catch (DomainException $exception) {
            $this->error($exception->getMessage());

            Log::notice('staff.bootstrap.refused', ['reason' => 'policy']);

            return self::FAILURE;
        } catch (Throwable $exception) {
            Log::error('staff.bootstrap.failed', [
                'exception_type' => $exception::class,
            ]);

            $this->error(
                'Bootstrap failed. Inspect the database state before retrying.',
            );

            return self::FAILURE;
        }
    }

    /**
     * @throws Throwable
     */
    private function bootstrap(BootstrapAdministrator $bootstrap): int
    {
        $connection = (new User)->getConnection();

        if ($connection->getDriverName() !== 'pgsql') {
            throw new DomainException('Bootstrap requires PostgreSQL.');
        }

        $target = $connection->selectOne(
            'SELECT current_database() AS database, current_user AS role',
        );

        $this->table(
            ['database', 'Database role'],
            [[$target->database, $target->role]]
        );

        if (
            ! $connection->getSchemaBuilder()->hasTable('audit_events')
            || ! $connection->getSchemaBuilder()->hasTable('user_roles')
        ) {
            throw new DomainException('Required foundation migrations are missing.');
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

        $email = Str::lower(trim((string) $this->ask('Administrator Email')));

        Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email', 'max:255']],
        )->validate();

        $matches = User::on($connection->getName())
            ->whereRaw('LOWER(email) = ?', [$email])
            ->get();

        if ($matches->count() > 1) {
            throw new DomainException('Ambiguous account email; bootstrap refused.');
        }

        $existing = $matches->first();
        $existingUserId = $existing?->id;
        $name = null;
        $password = null;

        if ($existing) {
            $this->line('Existing account ID: '.$existing->id);

            if (! $this->confirm(
                'Promote this account, preserving its identity, password and verification state?',
                false,
            )) {
                $this->info('Cancelled. No account changes made.');

                return self::SUCCESS;
            }
        } else {
            $name = trim((string) $this->ask('Administrator Name'));

            $password = (string) $this->secret(
                'Password: 12+ characters, mixed case, numbers and symbols',
                false,
            );

            $confirmation = (string) $this->secret('Confirm password', false);

            if (! hash_equals($password, $confirmation)) {
                throw new DomainException('Password confirmation does not match.');
            }
        }

        if (! $this->confirm(
            'Activate this account and grant Administrator on the displayed database?',
            false,
        )) {
            $this->info('Cancelled. No account changes made.');

            return self::SUCCESS;
        }

        $user = $bootstrap->execute(
            email: $email,
            existingUserId: $existingUserId,
            name: $name,
            password: $password,
        );

        unset($password, $confirmation);

        $this->info('Administrator bootstrap committed with its audit event.');

        if (! $user->hasVerifiedEmail()) {
            try {
                $user->sendEmailVerificationNotification();

                $this->info('Verification notification dispatched.');
            } catch (Throwable $exception) {
                Log::warning('staff.bootstrap.verification_delivery_failed', [
                    'subject_id' => $user->id,
                    'exception_type' => $exception::class,
                ]);

                $this->warn(
                    'Account committed; mail delivery failed. Log in and resend verification.',
                );
            }
        }

        return self::SUCCESS;
    }
}
