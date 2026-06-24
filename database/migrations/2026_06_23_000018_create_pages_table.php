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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_fr');
            $table->string('slug')->unique();
            $table->longText('content_en')->nullable();
            $table->longText('content_fr')->nullable();
            $table->string('status')->default('published')->index();
            $table->string('seo_title_en')->nullable();
            $table->string('seo_title_fr')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->text('meta_description_fr')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
