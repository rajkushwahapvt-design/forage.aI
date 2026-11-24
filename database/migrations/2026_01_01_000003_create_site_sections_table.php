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
        Schema::create('site_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('type'); // hero, about, services, pricing, why_us, testimonials, gallery, hours_location, contact, footer
            $table->string('title')->nullable();
            $table->json('content'); // structured data specific to section type
            $table->integer('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->text('last_ai_prompt')->nullable();
            $table->string('generation_source')->default('ai'); // ai, fallback_template, user_edited
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_sections');
    }
};
