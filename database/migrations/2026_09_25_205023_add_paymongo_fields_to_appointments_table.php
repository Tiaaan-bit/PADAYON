<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('paymongo_checkout_session_id')
                ->nullable()
                ->after('amount_paid');

            $table->string('paymongo_payment_id')
                ->nullable()
                ->after('paymongo_checkout_session_id');

            $table->decimal('payment_amount', 10, 2)
                ->nullable()
                ->after('paymongo_payment_id');

            $table->string('payment_status')
                ->default('pending')
                ->after('payment_amount');

            $table->timestamp('paid_at')
                ->nullable()
                ->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'paymongo_checkout_session_id',
                'paymongo_payment_id',
                'payment_amount',
                'payment_status',
                'paid_at',
            ]);
        });
    }
};