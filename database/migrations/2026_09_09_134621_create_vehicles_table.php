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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->string('vehicle_code', 30)->unique();
            $table->string('name', 100);

            $table->string('type', 50);
            $table->string('brand', 50)->nullable();
            $table->string('model', 100)->nullable();

            $table->string('registration_number', 30)
                ->nullable()
                ->unique();

            $table->string('status', 30)
                ->default('active')
                ->index();

            $table->date('purchase_date')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
