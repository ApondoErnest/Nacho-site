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
        Schema::create('centers', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_fr');
            $table->string('slug')->unique();
            $table->string('city_en');
            $table->string('city_fr');
            $table->string('region_en')->nullable();
            $table->string('region_fr')->nullable();
            $table->text('address_en')->nullable();
            $table->text('address_fr')->nullable();
            $table->string('postal_address')->nullable();
            $table->string('status')->default('active')->index();
            $table->boolean('is_headquarters')->default(false)->index();
            $table->boolean('booking_enabled')->default(false)->index();
            $table->text('description_en')->nullable();
            $table->text('description_fr')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('nearby_landmark')->nullable();
            $table->text('search_keywords')->nullable();
            $table->text('vehicle_categories_en')->nullable();
            $table->text('vehicle_categories_fr')->nullable();
            $table->string('featured_image')->nullable();
            $table->date('target_opening_date')->nullable();
            $table->string('target_date_text_en')->nullable();
            $table->string('target_date_text_fr')->nullable();
            $table->string('expansion_phase')->nullable();
            $table->timestamp('expansion_updated_at')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centers');
    }
};
