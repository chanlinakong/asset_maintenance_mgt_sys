<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_parts', function (Blueprint $table) {
            // First create a replacement normal index.
// This keeps the foreign key supported.
            $table->index(
                ['maintenance_record_id', 'part_id'],
                'maintenance_parts_record_part_index'
            );

            // Then remove the old unique constraint.
            $table->dropUnique(
                'maintenance_parts_maintenance_record_id_part_id_unique'
            );
            // Allow fractional quantities.
            $table->decimal('quantity', 12, 3)->change();

            // Link each part entry to its repair activity.
            // Nullable to preserve existing historical records.
            $table->foreignId('maintenance_repair_work_id')
                ->nullable()
                ->after('maintenance_record_id')
                ->constrained('maintenance_repair_works')
                ->nullOnDelete();

            // Preserve catalog information at the time of repair.
            $table->string('part_name', 150)->nullable();
            $table->string('part_code', 50)->nullable();
            $table->string('unit', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_parts', function (Blueprint $table) {
            $table->dropForeign([
                'maintenance_repair_work_id',
            ]);

            $table->dropIndex(
                'maintenance_parts_record_part_index'
            );

            $table->dropColumn([
                'maintenance_repair_work_id',
                'part_name',
                'part_code',
                'unit',
            ]);

            // Reverting this can lose fractional quantities.
            $table->unsignedInteger('quantity')->change();

            // Restore the original restriction.
            $table->unique([
                'maintenance_record_id',
                'part_id',
            ]);
        });
    }
};
