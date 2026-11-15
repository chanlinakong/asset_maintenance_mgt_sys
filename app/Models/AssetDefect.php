<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetDefect extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'defect_type_id',
        'recurrence_of_defect_id',
        'title',
        'description',
        'status',
        'reported_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function defectType(): BelongsTo
    {
        return $this->belongsTo(DefectType::class);
    }

    public function maintenanceRecordDefects(): HasMany
    {
        return $this->hasMany(MaintenanceRecordDefect::class);
    }

    public function previousOccurrence(): BelongsTo
    {
        return $this->belongsTo(
            AssetDefect::class,
            'recurrence_of_defect_id'
        );
    }

    public function recurrenceOccurrences(): HasMany
    {
        return $this->hasMany(
            AssetDefect::class,
            'recurrence_of_defect_id'
        );
    }
}
