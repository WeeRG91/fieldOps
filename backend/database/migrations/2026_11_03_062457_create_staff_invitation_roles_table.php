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
        Schema::create('staff_invitation_roles', function (Blueprint $table) {
            $table->foreignId('invitation_id')
                ->constrained('staff_invitations')
                ->restrictOnDelete();

            $table->enum('role', [
                'administrator',
                'dispatcher',
                'technician',
            ]);

            $table->primary(['invitation_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_invitation_roles');
    }
};
