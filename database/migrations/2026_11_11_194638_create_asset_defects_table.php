
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_defects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->restrictOnDelete();

            $table->foreignId('defect_type_id')
                ->constrained('defect_types')
                ->restrictOnDelete();

            $table->string('title', 200);

            $table->text('description')->nullable();

            $table->string('status', 30)
                ->default('open')
                ->index();

            $table->dateTime('reported_at')
                ->useCurrent();

            $table->dateTime('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['vehicle_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_defects');
    }
};
