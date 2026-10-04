<div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    <div>
        <label class="mb-2 block text-sm font-medium">
            Vehicle Code
        </label>

        <input type="text" name="vehicle_code" value="{{ old('vehicle_code', $vehicle->vehicle_code ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('vehicle_code')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label class="mb-2 block text-sm font-medium">
            Vehicle Name
        </label>

        <input type="text" name="name" value="{{ old('name', $vehicle->name ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('name')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label class="mb-2 block text-sm font-medium">
            Type
        </label>

        <input type="text" name="type" placeholder="Truck, Forklift, Crane..."
            value="{{ old('type', $vehicle->type ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('type')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label class="mb-2 block text-sm font-medium">
            Brand
        </label>

        <input type="text" name="brand" value="{{ old('brand', $vehicle->brand ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('brand')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>


    <div>
        <label class="mb-2 block text-sm font-medium">
            Model
        </label>

        <input type="text" name="model" value="{{ old('model', $vehicle->model ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('model')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label class="mb-2 block text-sm font-medium">
            Registration Number
        </label>

        <input type="text" name="registration_number" value="{{ old(
    'registration_number',
    $vehicle->registration_number ?? ''
) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('registration_number')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="current_kilometers" class="block text-sm font-medium text-gray-700">
            Current Kilometers
        </label>

        <input type="number" name="current_kilometers" id="current_kilometers"
            value="{{ old('current_kilometers', $vehicle->current_kilometers) }}" min="0" step="1"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
            placeholder="e.g. 50000">

        @error('current_kilometers')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror

        <p class="mt-1 text-xs text-gray-500">
            Enter the vehicle's current odometer reading.
        </p>
    </div>



    @if ($vehicle->exists)
        @if ($vehicle->status !== \App\Enums\VehicleStatus::Maintenance)
            <div>
                <label class="mb-2 block text-sm font-medium">
                    Status
                </label>

                <input type="hidden" name="status" value="active">

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="status" value="out_of_service" @checked(
                        old(
                            'status',
                            $vehicle->status?->value
                        ) === 'out_of_service'
                    )
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                    <span class="text-sm text-gray-700">
                        Out of Service
                    </span>
                </label>

                @error('status')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        @else
            <div>
                <label class="mb-2 block text-sm font-medium">
                    Status
                </label>

                <p class="text-sm font-medium text-gray-700">
                    Maintenance
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    Status is managed by the active maintenance record.
                </p>
            </div>
        @endif
    @endif




    <div>
        <label class="mb-2 block text-sm font-medium">
            Purchase Date
        </label>

        <input type="date" name="purchase_date" value="{{ old(
    'purchase_date',
    isset($vehicle) && $vehicle->purchase_date
    ? $vehicle->purchase_date->format('Y-m-d')
    : ''
) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('purchase_date')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

</div>


<div class="mt-5">
    <label class="mb-2 block text-sm font-medium">
        Notes
    </label>

    <textarea name="notes" rows="4"
        class="w-full rounded-lg border border-gray-300 px-3 py-2">{{ old('notes', $vehicle->notes ?? '') }}</textarea>
</div>