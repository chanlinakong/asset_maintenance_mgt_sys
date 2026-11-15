<?php

namespace App\Models;

use App\Enums\MaintenanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class MaintenanceAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_record_id',
        'maintenance_visit_id',
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

    public function visit(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceVisit::class,
            'maintenance_visit_id'
        );
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    public function destroyGeneral(
        MaintenanceRecord $maintenance,
        MaintenanceAttachment $attachment
    ): RedirectResponse {
        $this->authorize('update', $maintenance);

        abort_unless(
            $maintenance->status === MaintenanceStatus::InProgress,
            403
        );

        abort_unless(
            (int) $attachment->maintenance_record_id ===
            (int) $maintenance->id
            && $attachment->maintenance_visit_id === null,
            404
        );

        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return back()->with(
            'success',
            'General attachment deleted successfully.'
        );
    }
}