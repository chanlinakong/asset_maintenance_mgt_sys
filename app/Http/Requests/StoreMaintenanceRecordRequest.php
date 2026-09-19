<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

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
        });
    }
}