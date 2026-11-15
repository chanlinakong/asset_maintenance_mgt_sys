<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceAttachment;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceVisit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Throwable;

class MaintenanceAttachmentController extends Controller
{
    private function verifyVisitOwnership(
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit
    ): void {
        Gate::authorize('update', $maintenance);

        abort_unless(
            $maintenance->status === MaintenanceStatus::InProgress,
            403
        );

        abort_unless(
            (int) $visit->maintenance_record_id ===
                (int) $maintenance->id,
            404
        );

        abort_unless(
            $visit->status === 'in_progress',
            403
        );
    }

    public function store(
        Request $request,
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit
    ): RedirectResponse {
        $this->verifyVisitOwnership($maintenance, $visit);

        $data = $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp,pdf',
            ],

            'attachment_type' => [
                'required',
                Rule::in([
                    'before',
                    'after',
                    'invoice',
                    'report',
                    'other',
                ]),
            ],
        ]);

        $file = $request->file('file');

        $path = $file->store(
            'maintenance/' . $maintenance->id .
                '/visits/' . $visit->id,
            'public'
        );

        try {
            MaintenanceAttachment::create([
                'maintenance_record_id' => $maintenance->id,
                'maintenance_visit_id' => $visit->id,
                'uploaded_by' => $request->user()->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'attachment_type' => $data['attachment_type'],
            ]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);

            throw $exception;
        }

        return back()->with(
            'success',
            'Attachment uploaded successfully.'
        );
    }

    public function destroy(
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceAttachment $attachment
    ): RedirectResponse {
        $this->verifyVisitOwnership($maintenance, $visit);

        abort_unless(
            (int) $attachment->maintenance_record_id ===
                (int) $maintenance->id
            && (int) $attachment->maintenance_visit_id ===
                (int) $visit->id,
            404
        );

        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return back()->with(
            'success',
            'Attachment deleted successfully.'
        );
    }
}
