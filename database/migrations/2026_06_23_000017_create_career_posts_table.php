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
        Schema::create('career_posts', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('title_en');
            $table->string('title_fr');
            $table->string('slug')->unique();
            $table->foreignId('department_id')->constrained('career_departments')->restrictOnDelete();
            $table->foreignId('center_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employment_type')->nullable()->index();
            $table->text('summary_en')->nullable();
            $table->text('summary_fr')->nullable();
            $table->longText('description_en')->nullable();
            $table->longText('description_fr')->nullable();
            $table->longText('responsibilities_en')->nullable();
            $table->longText('responsibilities_fr')->nullable();
            $table->longText('requirements_en')->nullable();
            $table->longText('requirements_fr')->nullable();
            $table->longText('preferred_requirements_en')->nullable();
            $table->longText('preferred_requirements_fr')->nullable();
            $table->longText('skills_en')->nullable();
            $table->longText('skills_fr')->nullable();
            $table->text('application_documents_en')->nullable();
            $table->text('application_documents_fr')->nullable();
            $table->string('application_email')->nullable();
            $table->string('application_subject')->nullable();
            $table->text('application_instructions_en')->nullable();
            $table->text('application_instructions_fr')->nullable();
            $table->unsignedInteger('vacancies_count')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->date('closes_at')->nullable()->index();
            $table->string('status')->default('draft')->index();
            $table->boolean('allow_email_application')->default(true);
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('career_posts');
    }
};
