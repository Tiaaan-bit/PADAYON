<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Who booked
            $table->foreignId('service_id')->constrained()->cascadeOnDelete(); // Selected service
            $table->foreignId('therapist_id')->constrained()->cascadeOnDelete(); // Selected therapist
            $table->decimal('service_price', 10, 2); // Saved service price
            $table->integer('service_duration_minutes'); // Saved service duration
            $table->string('level'); // gentle, mild, hard
            $table->string('has_previous_operations')->nullable(); // yes or no
            $table->text('body_problem')->nullable(); // Note or body concern
            $table->date('appointment_date'); // Chosen date
            $table->time('appointment_time'); // Start time only
            $table->time('appointment_end_time'); // Computed end time
            $table->string('payment_method'); // branch or gcash
            $table->string('payment_type')->nullable(); // downpayment or full, only for gcash
            $table->decimal('amount_paid', 10, 2)->default(0); // Amount paid now
            $table->string('status')->default('pending'); // Default status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_appointments');
    }
};
