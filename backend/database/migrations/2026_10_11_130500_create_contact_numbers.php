<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_setting_id')->constrained()->restrictOnDelete();
            $table->string('phone', 20);
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['business_setting_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_numbers');
    }
};
