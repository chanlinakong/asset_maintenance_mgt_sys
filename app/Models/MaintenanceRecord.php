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
        'service_provider',
        'notes',
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
}