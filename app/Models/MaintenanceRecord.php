<?php

namespace App\Models;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'title',
        'description',
        'type',
        'status',
        'reported_at',
        'started_at',
        'completed_at',
        'cost',
        'service_kilometers',
        'service_provider',
        'notes',
        'maintenance_schedule_id',
        'parts_cost',
        'labor_cost',
        'other_cost',
    ];

    protected function casts(): array
    {
        return [
            'type' => MaintenanceType::class,
            'status' => MaintenanceStatus::class,

            'reported_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',

            'cost' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(
            MaintenanceAudit::class
        )->latest();
    }

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceSchedule::class
        );
    }

    public function maintenanceParts(): HasMany
    {
        return $this->hasMany(
            MaintenancePart::class
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(
            MaintenanceAttachment::class
        )->latest();
    }
}