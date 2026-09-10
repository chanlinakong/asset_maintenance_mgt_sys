<?php

namespace App\Models;

use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_code',
        'name',
        'type',
        'brand',
        'model',
        'registration_number',
        'status',
        'purchase_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => VehicleStatus::class,
            'purchase_date' => 'date',
        ];
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}