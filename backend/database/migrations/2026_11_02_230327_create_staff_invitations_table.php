<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Schema::getConnection();

        if ($connection->getDriverName() !== 'pgsql') {
            throw new LogicException(
                'Staff invitation constraints currently require PostgreSQL.',
            );
        }

        Schema::create('staff_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255);
            $table->char('token_hash', 64)->unique();

            $table->foreignId('invited_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestampTz('expires_at');
            $table->timestampTz('accepted_at')->nullable();

            $table->foreignId('accepted_user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->unique('accepted_user_id');

            $table->timestampTz('revoked_at')->nullable();

            $table->foreignId('revoked_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestampTz('superseded_at')->nullable();
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');
        });

        $connection->statement(<<<'SQL'
            ALTER TABLE staff_invitations
                ADD CONSTRAINT staff_invitations_email_normalized
                    CHECK (
                        email = LOWER(BTRIM(email))
                        AND email <> ''
                    ),
                ADD CONSTRAINT staff_invitations_token_hash_format
                    CHECK (token_hash ~ '^[0-9a-f]{64}$'),
                ADD CONSTRAINT staff_invitations_expiry_after_creation
                    CHECK (expires_at > created_at),
                ADD CONSTRAINT staff_invitations_acceptance_pair
                    CHECK (
                        (accepted_at IS NULL) = (accepted_user_id IS NULL)
                    ),
                ADD CONSTRAINT staff_invitations_revocation_pair
                    CHECK (
                        (revoked_at IS NULL) = (revoked_by IS NULL)
                    ),
                ADD CONSTRAINT staff_invitations_single_terminal_state
                    CHECK (
                        (accepted_at IS NULL OR revoked_at IS NULL)
                        AND (accepted_at IS NULL OR superseded_at IS NULL)
                        AND (revoked_at IS NULL OR superseded_at IS NULL)
                    )
            SQL);

        $connection->statement(<<<'SQL'
            CREATE UNIQUE INDEX staff_invitations_one_open_email
                ON staff_invitations (email)
                WHERE accepted_at IS NULL
                  AND revoked_at IS NULL
                  AND superseded_at IS NULL
            SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_invitations');
    }
};
