
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_repair_works', function (Blueprint $table) {
            $table->id();

            $table->foreignId('maintenance_record_defect_id')
                ->constrained('maintenance_record_defects')
                ->cascadeOnDelete();

            $table->text('work_description');

            $table->decimal('quantity', 12, 3)
                ->nullable();

            $table->string('unit', 30)->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_repair_works');
    }
};
