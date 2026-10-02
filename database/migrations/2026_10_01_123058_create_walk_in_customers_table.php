<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('walk_in_customers', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('phone', 20);

            $table->string('email')->nullable();

            /*
             * If the walk-in customer later creates
             * a normal Padayon account, this connects
             * the walk-in record to the users table.
             */
            $table->foreignId('registered_user_id')->nullable()->constrained('users')->nullOnDelete();

            /*
             * Registration invitation.
             */
            $table->string('registration_token', 64)->nullable()->unique();

            $table->timestamp('registration_token_expires_at')->nullable();

            $table->timestamps();

            $table->index('phone');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('walk_in_customers');
    }
};
