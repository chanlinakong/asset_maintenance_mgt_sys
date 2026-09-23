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
        Schema::create('parts', function (Blueprint $table) {
            $table->id();

            $table->string('part_code', 50)
                ->unique();

            $table->string('name', 150);

            $table->string('category', 100)
                ->nullable();

            $table->string('unit', 30)
                ->default('piece');

            $table->decimal('default_unit_cost', 15, 2)
                ->default(0);

            $table->unsignedInteger('stock_quantity')
                ->default(0);

            $table->boolean('is_active')
                ->default(true)
                ->index();

            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
