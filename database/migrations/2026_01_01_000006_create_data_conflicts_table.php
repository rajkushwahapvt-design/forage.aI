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
        Schema::create('data_conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('field'); // phone, address, business_hours, email, etc.
            $table->string('source_a'); // e.g. "Google Business Listing"
            $table->text('value_a');
            $table->string('source_b'); // e.g. "Uploaded PDF Brochure"
            $table->text('value_b');
            $table->enum('status', ['pending', 'resolved'])->default('pending');
            $table->text('resolved_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_conflicts');
    }
};
