<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Enums\VehicleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'report_type' => [
                'required',
                Rule::in([
                    'vehicle_status',
                    'maintenance_history',
                    'monthly_maintenance',
                    'maintenance_schedule',
                    'maintenance_cost',
                ]),
            ],

            'period' => [
                'required',
                Rule::in([
                    'all',
                    'today',
                    'yesterday',
                    'this_month',
                    'last_month',
                    'last_3_months',
                    'this_year',
                    'last_year',
                    'custom',
                ]),
            ],

            'from' => [
                'nullable',
                'required_if:period,custom',
                'date',
            ],

            'to' => [
                'nullable',
                'required_if:period,custom',
                'date',
                'after_or_equal:from',
            ],

            'vehicle_status' => [
                'nullable',
                Rule::in(
                    collect(VehicleStatus::cases())
                        ->map(fn ($case) => $case->value)
                        ->all()
                ),
            ],

            'maintenance_status' => [
                'nullable',
                Rule::in(
                    collect(MaintenanceStatus::cases())
                        ->map(fn ($case) => $case->value)
                        ->all()
                ),
            ],

            'maintenance_type' => [
                'nullable',
                Rule::in(
                    collect(MaintenanceType::cases())
                        ->map(fn ($case) => $case->value)
                        ->all()
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'report_type.required' => 'Please select a report type.',
            'report_type.in' => 'The selected report type is invalid.',
            'period.required' => 'Please select a date range.',
            'period.in' => 'The selected date range is invalid.',
            'from.required_if' => 'Please select a start date.',
            'to.required_if' => 'Please select an end date.',
            'to.after_or_equal' => 'The end date must be on or after the start date.',
        ];
    }
}