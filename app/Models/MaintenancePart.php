<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenancePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_record_id',
        'maintenance_repair_work_id',
        'part_id',
        'part_name',
        'part_code',
        'unit',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecord::class);
    }

    public function repairWork(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceRepairWork::class,
            'maintenance_repair_work_id'
        );
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
