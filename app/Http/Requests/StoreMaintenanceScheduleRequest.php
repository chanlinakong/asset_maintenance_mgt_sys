<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreMaintenanceScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                'integer',
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

            'last_service_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'interval_days' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'interval_kilometers' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    public function withValidator(
        Validator $validator
    ): void {
        $validator->after(function (Validator $validator) {

            if (
                !$this->filled('interval_days')
                && !$this->filled('interval_kilometers')
            ) {
                $validator->errors()->add(
                    'interval_days',
                    'At least one maintenance interval is required.'
                );
            }
        });
    }
}
