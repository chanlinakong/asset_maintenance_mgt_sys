<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                'exists:maintenance_schedules,id',
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
        });
    }
}