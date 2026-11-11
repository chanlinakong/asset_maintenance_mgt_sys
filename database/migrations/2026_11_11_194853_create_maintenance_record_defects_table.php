<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('maintenance_record_defects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('maintenance_record_id')
                ->constrained('maintenance_records')
                ->cascadeOnDelete();

            $table->foreignId('asset_defect_id')
                ->constrained('asset_defects')
                ->restrictOnDelete();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique(
                ['maintenance_record_id', 'asset_defect_id'],
                'mrd_record_defect_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_record_defects');
    }
};
