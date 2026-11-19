<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VehicleAttachmentController extends Controller
{
    public function store(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validated = $request->validate([
            'files' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            'files.*' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png,webp',
                'max:10240',
            ],
        ], [
            'files.*.mimes' =>
                'Only PDF, DOC, DOCX, JPG, JPEG, PNG and WEBP files are allowed.',

            'files.*.max' =>
                'Each file must not exceed 10 MB.',
        ]);

        $attachments = [];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');

        foreach ($validated['files'] as $file) {
            $path = $file->store('vehicle', 'public');

            if (!is_string($path) || $path === '') {
                throw new \RuntimeException(
                    "Failed to store uploaded file: {$file->getClientOriginalName()}"
                );
            }

            if (!$publicDisk->exists($path)) {
                throw new \RuntimeException(
                    "Uploaded file was not found in public storage: {$path}"
                );
            }

            try {
                $attachment = $vehicle->attachments()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            } catch (\Throwable $e) {
                Storage::disk('public')->delete($path);

                throw $e;
            }

            $attachments[] = [
                'id' => $attachment->id,
                'name' => $attachment->original_name,
                'size' => $attachment->file_size,
                'mime' => $attachment->mime_type,
                'extension' => strtolower($file->getClientOriginalExtension()),
                'url' => $publicDisk->url($attachment->file_path),
            ];
        }

        return response()->json([
            'message' => 'Files uploaded successfully.',
            'attachments' => $attachments,
        ], 201);
    }

    public function destroy(
        Vehicle $vehicle,
        VehicleAttachment $attachment
    ): JsonResponse {
        if ($attachment->vehicle_id !== $vehicle->id) {
            abort(404);
        }

        DB::transaction(function () use ($attachment) {
            Storage::disk('public')->delete($attachment->file_path);

            $attachment->delete();
        });

        return response()->json([
            'message' => 'Attachment deleted successfully.',
        ]);
    }
}
