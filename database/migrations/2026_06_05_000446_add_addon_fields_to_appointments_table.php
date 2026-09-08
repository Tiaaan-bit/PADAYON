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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('add_on_id')
                ->nullable()
                ->after('level')
                ->constrained('add_ons')
                ->nullOnDelete();

            $table->decimal('addons_price', 10, 2)->nullable()->default(0)->after('add_on_id');
            $table->integer('addons_duration_minutes')->nullable()->default(0)->after('addons_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('add_on_id');
            $table->dropColumn(['addons_price', 'addons_duration_minutes']);
        });
    }
};
