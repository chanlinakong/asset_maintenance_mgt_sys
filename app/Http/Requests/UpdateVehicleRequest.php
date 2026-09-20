<?php

namespace App\Http\Requests;

use App\Enums\VehicleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Vehicle;
use Illuminate\Validation\Validator;

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
            'current_kilometers' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }

    public function withValidator(
        Validator $validator
    ): void {
        $validator->after(function (Validator $validator) {

            /** @var Vehicle|null $vehicle */
            $vehicle = $this->route('vehicle');

            if (!$vehicle) {
                return;
            }

            $newKilometers =
                $this->input('current_kilometers');

            if (
                $newKilometers !== null
                && $vehicle->current_kilometers !== null
                && (int) $newKilometers
                < $vehicle->current_kilometers
            ) {
                $validator->errors()->add(
                    'current_kilometers',
                    'Current kilometers cannot be lower than the existing vehicle reading.'
                );
            }
        });
    }
}