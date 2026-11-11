<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRepairWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_record_defect_id',
        'work_description',
        'quantity',
        'unit',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
        ];
    }

    public function maintenanceRecordDefect(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecordDefect::class);
    }

    public function maintenanceParts(): HasMany
    {
        return $this->hasMany(MaintenancePart::class);
    }
}

