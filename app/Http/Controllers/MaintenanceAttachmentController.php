<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceAttachmentRequest;
use App\Models\MaintenanceAttachment;
use App\Models\MaintenanceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class MaintenanceAttachmentController extends Controller
{
    public function store(
        StoreMaintenanceAttachmentRequest $request,
        MaintenanceRecord $maintenance
    ): RedirectResponse {

        // $this->authorize(
        //     'update',
        //     $maintenance
        // );

        $file = $request->file('file');

        $path = $file->store(
            'maintenance/' . $maintenance->id,
            'public'
        );

        MaintenanceAttachment::create([
            'maintenance_record_id' => $maintenance->id,

            'uploaded_by' =>
                $request->user()->id,

            'file_name' =>
                $file->getClientOriginalName(),

            'file_path' =>
                $path,

            'file_type' =>
                $file->getMimeType(),

            'file_size' =>
                $file->getSize(),
        ]);

        return back()->with(
            'success',
            'Attachment uploaded successfully.'
        );
    }

    public function destroy(
        MaintenanceRecord $maintenance,
        MaintenanceAttachment $attachment
    ): RedirectResponse {

        // $this->authorize(
        //     'update',
        //     $maintenance
        // );

        abort_unless(
            $attachment->maintenance_record_id
                === $maintenance->id,
            404
        );

        Storage::disk('public')
            ->delete($attachment->file_path);

        $attachment->delete();

        return back()->with(
            'success',
            'Attachment deleted successfully.'
        );
    }
}