<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\MaintenanceRecord;
use App\Enums\VehicleStatus;

class StoreMaintenanceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
            ],

            'title' => [
                'required',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'type' => [
                'required',
                Rule::enum(MaintenanceType::class),
            ],

            // 'status' => [
            //     MaintenanceStatus::Pending->value,
            // ],

            'reported_at' => [
                'required',
                'date',
            ],

            'started_at' => [
                'nullable',
                'date',
                'after_or_equal:reported_at',
                'required_if:status,in_progress,completed',
                'prohibited_if:status,pending,cancelled',
            ],

            'completed_at' => [
                'nullable',
                'date',
                'after_or_equal:started_at',
                'required_if:status,completed',
                'prohibited_if:status,pending,cancelled',
            ],

            'service_kilometers' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'service_provider' => [
                'nullable',
                'string',
                'max:150',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
            'maintenance_schedule_id' => [
                'nullable',
                'integer',
                Rule::exists('maintenance_schedules', 'id')
                    ->where(function ($query) {
                        $query->where(
                            'vehicle_id',
                            $this->input('vehicle_id')
                        )->where('is_active', true);
                    }),
            ],
            'defect_ids' => [
                'nullable',
                'array',
            ],

            'defect_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:asset_defects,id',
            ],

            'new_defects' => [
                'nullable',
                'array',
                'max:20',
            ],

            'new_defects.*.defect_type_id' => [
                'required',
                'integer',
                Rule::exists('defect_types', 'id')
                    ->where('is_active', true),
            ],

            'new_defects.*.title' => [
                'required',
                'string',
                'max:200',
            ],

            'new_defects.*.description' => [
                'nullable',
                'string',
            ],

        ];
    }
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            // Validate that selected defects belong to this vehicle
// and are not already being handled by another active report.

            $vehicleId = $this->input('vehicle_id');

            $defectIds = $this->input('defect_ids', []);

            if (is_array($defectIds) && count($defectIds) > 0) {

                $validDefectIds = \App\Models\AssetDefect::query()
                    ->whereIn('id', $defectIds)
                    ->where('vehicle_id', $vehicleId)
                    ->whereIn('status', ['open', 'in_progress'])
                    ->whereDoesntHave(
                        'maintenanceRecordDefects',
                        function ($query) {
                            $query->whereHas(
                                'maintenanceRecord',
                                function ($maintenanceQuery) {
                                    $maintenanceQuery->whereIn('status', [
                                        MaintenanceStatus::Pending->value,
                                        MaintenanceStatus::InProgress->value,
                                    ]);
                                }
                            );
                        }
                    )
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->all();

                $submittedIds = array_map('intval', $defectIds);

                $invalidIds = array_diff($submittedIds, $validDefectIds);

                if (!empty($invalidIds)) {
                    $validator->errors()->add(
                        'defect_ids',
                        'One or more selected defects do not belong to this vehicle, are already resolved, or are being handled by another active maintenance report.'
                    );
                }
            }

            $vehicle = \App\Models\Vehicle::find(
                $this->input('vehicle_id')
            );

            if (
                $vehicle
                && $vehicle->status === VehicleStatus::OutOfService
            ) {
                $validator->errors()->add(
                    'vehicle_id',
                    'Maintenance cannot be created for a vehicle that is out of service.'
                );
            }

            $serviceKilometers =
                $this->input('service_kilometers');

            if (
                $vehicleId
                && $serviceKilometers !== null
            ) {
                $vehicle = \App\Models\Vehicle::find(
                    $this->input('vehicle_id')
                );

                if (
                    $vehicle
                    && $this->filled('service_kilometers')
                    && $vehicle->current_kilometers !== null
                    && (int) $this->input('service_kilometers')
                    < $vehicle->current_kilometers
                ) {
                    $validator->errors()->add(
                        'service_kilometers',
                        'Service kilometers cannot be lower than the vehicle current kilometer reading.'
                    );
                }
            }

            $status = MaintenanceStatus::tryFrom(
                $this->input('status')
            );

            if (!$status) {
                return;
            }

            $startedAt = $this->input('started_at');
            $completedAt = $this->input('completed_at');

            if (
                $status === MaintenanceStatus::Pending
                && ($startedAt || $completedAt)
            ) {
                $validator->errors()->add(
                    'status',
                    'Pending maintenance cannot have start or completion dates.'
                );
            }

            if (
                $status === MaintenanceStatus::InProgress
                && $completedAt
            ) {
                $validator->errors()->add(
                    'completed_at',
                    'An in-progress maintenance cannot have a completion date.'
                );
            }

            if (
                $status === MaintenanceStatus::Cancelled
                && ($startedAt || $completedAt)
            ) {
                $validator->errors()->add(
                    'status',
                    'Cancelled maintenance cannot have start or completion dates.'
                );
            }

            $type = MaintenanceType::tryFrom(
                $this->input('type')
            );

            $scheduleId =
                $this->input('maintenance_schedule_id');

            if (
                $type === MaintenanceType::Preventive
                && $scheduleId
                && $this->input('status') ===
                MaintenanceStatus::Completed->value
            ) {

                $schedule =
                    \App\Models\MaintenanceSchedule::find(
                        $scheduleId
                    );

                if (
                    $schedule
                    && $schedule->interval_kilometers
                    && !$this->filled('service_kilometers')
                ) {
                    $validator->errors()->add(
                        'service_kilometers',
                        'Service kilometers are required for this kilometer-based preventive maintenance.'
                    );
                }
            }

            //Prevent duplicate Pending and In progress for the same schedule
            if (
                $type === MaintenanceType::Preventive
                && $scheduleId
            ) {

                $exists = MaintenanceRecord::query()
                    ->where(
                        'maintenance_schedule_id',
                        $scheduleId
                    )
                    ->whereIn('status', [
                        MaintenanceStatus::Pending->value,
                        MaintenanceStatus::InProgress->value,
                    ])
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'maintenance_schedule_id',
                        'This maintenance schedule already has an active maintenance record.'
                    );
                }
            }
        });
    }
}