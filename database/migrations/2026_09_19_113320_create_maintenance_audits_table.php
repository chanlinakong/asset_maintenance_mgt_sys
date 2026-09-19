<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_audits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('maintenance_record_id')
                ->constrained('maintenance_records')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('action', 30);

            $table->string('old_status', 30)->nullable();

            $table->string('new_status', 30)->nullable();

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_audits');
    }
};