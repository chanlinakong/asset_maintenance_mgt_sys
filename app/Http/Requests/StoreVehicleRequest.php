<?php

namespace App\Http\Requests;

use App\Enums\VehicleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_code' => [
                'required',
                'string',
                'max:30',
                'unique:vehicles,vehicle_code',
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
                'unique:vehicles,registration_number',
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