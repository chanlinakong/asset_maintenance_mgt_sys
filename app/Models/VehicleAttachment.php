<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleAttachment extends Model
{
    protected $fillable = [
        'vehicle_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
