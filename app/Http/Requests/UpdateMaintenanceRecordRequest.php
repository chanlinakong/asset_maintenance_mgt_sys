<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\MaintenanceRecord;

class UpdateMaintenanceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maintenance = $this->route('maintenance');

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

            'status' => [
                'required',
                Rule::enum(MaintenanceStatus::class),
            ],

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

            'cost' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
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
                        );
                    }),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $maintenance = $this->route('maintenance');

            if (!$maintenance) {
                return;
            }

            $currentStatus = $maintenance->status;

            $newStatus = MaintenanceStatus::tryFrom(
                $this->input('status')
            );

            if (!$newStatus) {
                return;
            }

            if (
                $currentStatus === $newStatus
            ) {
                return;
            }

            if (
                !$currentStatus->canTransitionTo($newStatus)
            ) {
                $validator->errors()->add(
                    'status',
                    "Maintenance status cannot be changed from "
                    . str($currentStatus->value)
                        ->replace('_', ' ')
                        ->title()
                    . " to "
                    . str($newStatus->value)
                        ->replace('_', ' ')
                        ->title()
                    . "."
                );
            }

            $type = MaintenanceType::tryFrom(
                $this->input('type')
            );

            $scheduleId = $this->input(
                'maintenance_schedule_id'
            );

            if (
                $type === MaintenanceType::Preventive
                && !$scheduleId
            ) {
                $validator->errors()->add(
                    'maintenance_schedule_id',
                    'A preventive maintenance record must have a maintenance schedule.'
                );
            }

            $vehicleId = $this->input('vehicle_id');

            $serviceKilometers =
                $this->input('service_kilometers');

            if (
                $vehicleId
                && $serviceKilometers !== null
            ) {
                $vehicle = \App\Models\Vehicle::find(
                    $vehicleId
                );

                if (
                    $vehicle
                    && $vehicle->current_kilometers !== null
                    && (int) $serviceKilometers
                    < $vehicle->current_kilometers
                ) {
                    $validator->errors()->add(
                        'service_kilometers',
                        'Service kilometers cannot be lower than the vehicle current kilometers.'
                    );
                }
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
                && $maintenance
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
                    ->where('id', '!=', $maintenance->id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'maintenance_schedule_id',
                        'This maintenance schedule already has another active maintenance record.'
                    );
                }
            }
        });
    }
}