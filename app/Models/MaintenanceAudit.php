<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_record_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'description',
    ];

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceRecord::class
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}