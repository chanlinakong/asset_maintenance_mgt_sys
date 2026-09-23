<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_code',
        'name',
        'category',
        'unit',
        'default_unit_cost',
        'stock_quantity',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'default_unit_cost' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function maintenanceParts(): HasMany
    {
        return $this->hasMany(
            MaintenancePart::class
        );
    }
}