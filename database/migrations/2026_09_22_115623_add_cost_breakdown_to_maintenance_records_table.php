<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->decimal('parts_cost', 15, 2)
                ->default(0)
                ->after('cost');

            $table->decimal('labor_cost', 15, 2)
                ->default(0)
                ->after('parts_cost');

            $table->decimal('other_cost', 15, 2)
                ->default(0)
                ->after('labor_cost');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropColumn([
                'parts_cost',
                'labor_cost',
                'other_cost',
            ]);
        });
    }
};