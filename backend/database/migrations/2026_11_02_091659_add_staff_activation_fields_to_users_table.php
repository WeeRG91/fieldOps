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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(false);
            $table->timestampTz('deactivated_at')->nullable();

            $table->foreignId('deactivated_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('deactivation_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deactivated_by');

            $table->dropColumn([
                'is_active',
                'deactivated_at',
                'deactivation_reason',
            ]);
        });
    }
};
