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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('city')->nullable()->index();
            $table->string('state')->nullable();
            $table->string('country')->default('India');
            $table->string('pincode')->nullable();
            $table->text('full_address')->nullable();
            $table->string('category')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('google_place_id')->nullable()->index();
            $table->string('google_maps_url')->nullable();
            $table->json('social_links')->nullable(); // ['instagram' => ..., 'facebook' => ...]
            $table->json('business_hours')->nullable(); // ['monday' => '09:00 - 18:00', ...]
            $table->json('attributes')->nullable(); // amenities, payment methods, etc.
            $table->string('status')->default('draft'); // draft, analyzing, generated, published
            $table->boolean('has_unresolved_conflicts')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
