<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRecordDefect extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_record_id',
        'asset_defect_id',
        'remarks',
    ];

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecord::class);
    }

    public function assetDefect(): BelongsTo
    {
        return $this->belongsTo(AssetDefect::class);
    }

    public function repairWorks(): HasMany
    {
        return $this->hasMany(MaintenanceRepairWork::class);
    }
}
