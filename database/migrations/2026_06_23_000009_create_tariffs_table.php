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
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('category_code')->index();
            $table->string('category_slug')->unique();
            $table->string('name_en');
            $table->string('name_fr');
            $table->text('description_en')->nullable();
            $table->text('description_fr')->nullable();
            $table->unsignedInteger('price_fcfa');
            $table->unsignedInteger('validity_value');
            $table->string('validity_unit');
            $table->unsignedInteger('minimum_weight_kg')->nullable();
            $table->unsignedInteger('maximum_weight_kg')->nullable();
            $table->string('vehicle_icon')->nullable();
            $table->date('effective_date')->nullable()->index();
            $table->date('expiry_date')->nullable()->index();
            $table->string('regulatory_reference')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_bookable')->default(true)->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
