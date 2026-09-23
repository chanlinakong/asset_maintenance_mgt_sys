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
        Schema::create('maintenance_parts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('maintenance_record_id')
                ->constrained('maintenance_records')
                ->cascadeOnDelete();

            $table->foreignId('part_id')
                ->constrained('parts')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            $table->decimal('unit_cost', 15, 2);

            $table->decimal('total_cost', 15, 2);

            $table->timestamps();

            $table->unique([
                'maintenance_record_id',
                'part_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_parts');
    }
};
