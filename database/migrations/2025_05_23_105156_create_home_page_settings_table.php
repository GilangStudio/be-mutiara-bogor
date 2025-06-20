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
        Schema::create('home_page_settings', function (Blueprint $table) {
            $table->id();

            // Banner Section
            $table->string('banner_type')->default('image'); // image or video
            $table->string('banner_image_path')->nullable();
            $table->string('banner_video_path')->nullable();
            $table->string('banner_alt_text')->nullable();
            
            // Hero Section
            $table->string('hero_title');
            $table->text('hero_description');

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->string('brochure_file_path')->nullable();
            
            // About Development Section
            $table->string('about_section_title')->default('About The Development');
            $table->string('about_title');
            $table->text('about_description');
            $table->string('about_image_path');
            $table->string('about_image_alt_text')->nullable();
            $table->string('about_link_text')->default('Discover More');
            $table->string('about_link_url')->nullable();
            
            // Exclusive Features Section
            $table->string('features_section_title')->default('Exclusive Features');
            $table->string('features_title');
            $table->text('features_description')->nullable();
            $table->string('features_image_path');
            $table->string('features_image_alt_text')->nullable();
            $table->string('features_link_text')->default('Learn More');
            $table->string('features_link_url')->nullable();
            
            // Location Section
            $table->string('location_section_title')->default('Location & Accessibility');
            $table->string('location_title');
            $table->text('location_description');
            $table->string('location_image_path');
            $table->string('location_image_alt_text')->nullable();
            $table->string('location_link_text')->default('Get Direction');
            $table->string('location_link_url')->nullable();
            
            // Meta
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_settings');
    }
};
