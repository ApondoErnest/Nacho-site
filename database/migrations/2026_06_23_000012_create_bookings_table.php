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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->foreignId('center_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('tariff_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('vehicle_registration');
            $table->date('preferred_date')->index();
            $table->string('preferred_time');
            $table->string('document_path')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('consent')->default(false);
            $table->string('status')->default('pending')->index();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
