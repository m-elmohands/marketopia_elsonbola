<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('email')->nullable();
            $table->string('facebook_url', 2048)->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('map_url', 2048)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
        Schema::create('business_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_setting_id')->constrained()->cascadeOnDelete();
            $table->enum('locale', ['ar', 'en']);
            $table->string('brand_name');
            $table->string('tagline')->nullable();
            $table->text('address')->nullable();
            $table->text('opening_hours')->nullable();
            $table->text('quality_highlights')->nullable();
            $table->unique(['business_setting_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_setting_translations');
        Schema::dropIfExists('business_settings');
    }
};
