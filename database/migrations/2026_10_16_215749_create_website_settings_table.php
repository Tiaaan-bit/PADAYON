<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();

            // Facebook Page
            $table->string('facebook_name')->default('Padayon Massage Center');

            $table->string('facebook_url')->default('https://www.facebook.com/people/Padayon-Massage-Center/61583836714033/');

            // Location
            $table->string('business_address')->default('RCPJC Building Unit 1 Brgy. Salcedo 2, Noveleta, Philippines, 4105');

            $table->text('location_url')->nullable();

            // Contact
            $table->string('phone_number', 30)->default('0976 007 7035');

            // Daily working hours
            $table->time('opening_time')->default('13:00:00');
            $table->time('closing_time')->default('01:00:00');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
