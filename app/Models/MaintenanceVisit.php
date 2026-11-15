<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\MaintenanceRecordDefect;

class MaintenanceVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_record_id',
        'visit_number',
        'status',
        'technician_id',
        'started_at',
        'completed_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'visit_number' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecord::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function repairWorks(): HasMany
    {
        return $this->hasMany(
            MaintenanceRepairWork::class,
            'maintenance_visit_id'
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(
            MaintenanceAttachment::class,
            'maintenance_visit_id'
        )->latest();
    }

    public function recordDefects(): BelongsToMany
    {
        return $this->belongsToMany(
            MaintenanceRecordDefect::class,
            'maintenance_visit_defects',
            'maintenance_visit_id',
            'maintenance_record_defect_id'
        )->withTimestamps();
    }
}
