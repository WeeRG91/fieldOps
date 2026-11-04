<?php

use Illuminate\Database\Migrations\Migration;
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
                'Normalized email uniqueness currently requires PostgreSQL.',
            );
        }

        $hasCollisions = $connection->table('users')
            ->selectRaw('LOWER(BTRIM(email)) AS normalized_email')
            ->groupByRaw('LOWER(BTRIM(email))')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasCollisions) {
            throw new RuntimeException(
                'Normalized email collisions exist. Resolve them before migrating.',
            );
        }

        $connection->statement(<<<'SQL'
            CREATE UNIQUE INDEX users_normalized_email_unique
                ON users (LOWER(BTRIM(email)))
            SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::getConnection()->statement(
            'DROP INDEX IF EXISTS users_normalized_email_unique',
        );
    }
};
