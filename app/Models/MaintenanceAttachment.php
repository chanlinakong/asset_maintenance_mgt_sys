<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_record_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'attachment_type',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceRecord::class
        );
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }
}