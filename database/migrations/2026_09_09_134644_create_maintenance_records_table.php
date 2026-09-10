<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->restrictOnDelete();

            $table->string('title', 150);

            $table->text('description')->nullable();

            $table->string('type', 30)
                ->default('corrective')
                ->index();

            $table->string('status', 30)
                ->default('pending')
                ->index();

            $table->dateTime('reported_at');

            $table->dateTime('started_at')->nullable();

            $table->dateTime('completed_at')->nullable();

            $table->decimal('cost', 15, 2)
                ->default(0);

            $table->string('service_provider', 150)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
