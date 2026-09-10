<?php

namespace App\Http\Requests;

use App\Enums\VehicleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicle = $this->route('vehicle');

        return [
            'vehicle_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('vehicles', 'vehicle_code')
                    ->ignore($vehicle),
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'type' => [
                'required',
                'string',
                'max:50',
            ],

            'brand' => [
                'nullable',
                'string',
                'max:50',
            ],

            'model' => [
                'nullable',
                'string',
                'max:100',
            ],

            'registration_number' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('vehicles', 'registration_number')
                    ->ignore($vehicle),
            ],

            'status' => [
                'required',
                Rule::enum(VehicleStatus::class),
            ],

            'purchase_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}