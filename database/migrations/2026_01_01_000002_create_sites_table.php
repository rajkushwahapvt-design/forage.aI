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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('meta_description')->nullable();
            $table->string('theme')->default('modern'); // modern, bold, elegant, warm, tech
            $table->string('primary_color')->default('#2563EB'); // Hex or Tailwind class
            $table->string('secondary_color')->default('#1E40AF');
            $table->string('font_family')->default('Inter');
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->json('seo_metadata')->nullable(); // title, og:image, schema.org JSON-LD
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
