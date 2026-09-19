<?php

namespace App\Models;

use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\MaintenanceStatus;

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

    public function hasActiveMaintenance(): bool
    {
        return $this->maintenanceRecords()
            ->whereIn('status', [
                MaintenanceStatus::Pending->value,
                MaintenanceStatus::InProgress->value,
            ])
            ->exists();
    }

    public function syncMaintenanceStatus(): void
    {
        if ($this->hasActiveMaintenance()) {
            $this->update([
                'status' => VehicleStatus::Maintenance,
            ]);

            return;
        }

        if ($this->status === VehicleStatus::Maintenance) {
            $this->update([
                'status' => VehicleStatus::Active,
            ]);
        }
    }
}