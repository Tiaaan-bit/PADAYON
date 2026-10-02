<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /*
         * Remove the existing foreign key first.
         */
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        /*
         * Make user_id nullable and add walk-in fields.
         */
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();

            $table->foreignId('walk_in_customer_id')->nullable()->after('user_id')->constrained('walk_in_customers')->nullOnDelete();

            $table->string('booking_source')->default('online')->after('walk_in_customer_id');
        });

        /*
         * Re-add the users foreign key.
         */
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['walk_in_customer_id']);
            $table->dropForeign(['user_id']);

            $table->dropColumn(['walk_in_customer_id', 'booking_source']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
