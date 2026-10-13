<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->enum('key', ['home', 'about', 'contact'])->unique();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->enum('locale', ['ar', 'en']);
            $table->string('title');
            $table->text('intro')->nullable();
            $table->longText('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->unique(['page_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('pages');
    }
};
