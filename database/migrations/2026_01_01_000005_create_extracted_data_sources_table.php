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
        Schema::create('extracted_data_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('source_type'); // web_search, google_business, social_link, uploaded_pdf, uploaded_csv, user_input
            $table->string('source_url')->nullable();
            $table->longText('raw_content')->nullable();
            $table->json('parsed_facts')->nullable();
            $table->string('scrape_status')->default('success'); // success, blocked_anti_scraping, timeout, error
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extracted_data_sources');
    }
};
