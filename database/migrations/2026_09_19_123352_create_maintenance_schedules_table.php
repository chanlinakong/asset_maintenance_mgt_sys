<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->restrictOnDelete();

            $table->string('title', 150);

            $table->text('description')->nullable();

            $table->unsignedInteger('interval_days')->nullable();

            $table->unsignedInteger('interval_kilometers')->nullable();

            $table->date('last_service_date')->nullable();

            $table->date('next_due_date')->nullable();

            $table->unsignedInteger('last_service_kilometers')->nullable();

            $table->unsignedInteger('next_due_kilometers')->nullable();

            $table->boolean('is_active')
                ->default(true)
                ->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};