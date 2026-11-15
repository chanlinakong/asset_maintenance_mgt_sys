
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Create maintenance visits
        |--------------------------------------------------------------------------
        */

        Schema::create('maintenance_visits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('maintenance_record_id')
                ->constrained('maintenance_records')
                ->cascadeOnDelete();

            $table->unsignedInteger('visit_number');

            $table->string('status', 30)->default('in_progress')->index();

            $table->foreignId('technician_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique(
                ['maintenance_record_id', 'visit_number'],
                'maintenance_visit_number_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | 2. Add final outcome for each defect in a maintenance report
        |--------------------------------------------------------------------------
        */

        Schema::table('maintenance_record_defects', function (Blueprint $table) {
            $table->string('repair_outcome', 30)->nullable();

            $table->text('outcome_remarks')->nullable();

            $table->dateTime('outcome_recorded_at')->nullable();
        });

        /*
        |--------------------------------------------------------------------------
        | 3. Link repair activities to their maintenance visit
        |--------------------------------------------------------------------------
        */

        Schema::table('maintenance_repair_works', function (Blueprint $table) {
            $table->foreignId('maintenance_visit_id')
                ->nullable()
                ->constrained('maintenance_visits')
                ->nullOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | 4. Link attachments to their maintenance visit
        |--------------------------------------------------------------------------
        */

        Schema::table('maintenance_attachments', function (Blueprint $table) {
            $table->foreignId('maintenance_visit_id')
                ->nullable()
                ->constrained('maintenance_visits')
                ->nullOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | 5. Link recurring defects to previous defect occurrences
        |--------------------------------------------------------------------------
        */

        Schema::table('asset_defects', function (Blueprint $table) {
            $table->foreignId('recurrence_of_defect_id')
                ->nullable()
                ->constrained('asset_defects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Reverse the changes in the opposite order.

        Schema::table('asset_defects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recurrence_of_defect_id');
        });

        Schema::table('maintenance_attachments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('maintenance_visit_id');
        });

        Schema::table('maintenance_repair_works', function (Blueprint $table) {
            $table->dropConstrainedForeignId('maintenance_visit_id');
        });

        Schema::table('maintenance_record_defects', function (Blueprint $table) {
            $table->dropColumn([
                'repair_outcome',
                'outcome_remarks',
                'outcome_recorded_at',
            ]);
        });

        Schema::dropIfExists('maintenance_visits');
    }
};
