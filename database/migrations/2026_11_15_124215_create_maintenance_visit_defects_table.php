<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_visit_defects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('maintenance_visit_id')
                ->constrained('maintenance_visits')
                ->cascadeOnDelete();

            $table->foreignId('maintenance_record_defect_id')
                ->constrained('maintenance_record_defects')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['maintenance_visit_id', 'maintenance_record_defect_id'],
                'visit_defect_unique'
            );
        });

        /*
         * Recover historical visit-defect associations where
         * repair activities already exist.
         */
        $existingAssignments = DB::table('maintenance_repair_works')
            ->whereNotNull('maintenance_visit_id')
            ->select(
                'maintenance_visit_id',
                'maintenance_record_defect_id'
            )
            ->distinct()
            ->get();

        foreach ($existingAssignments as $assignment) {
            DB::table('maintenance_visit_defects')->insertOrIgnore([
                'maintenance_visit_id' => $assignment->maintenance_visit_id,
                'maintenance_record_defect_id' => $assignment->maintenance_record_defect_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_visit_defects');
    }
};
