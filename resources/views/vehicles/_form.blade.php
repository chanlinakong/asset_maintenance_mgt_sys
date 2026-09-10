<div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    <div>
        <label class="mb-2 block text-sm font-medium">
            Vehicle Code
        </label>

        <input
            type="text"
            name="vehicle_code"
            value="{{ old('vehicle_code', $vehicle->vehicle_code ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >

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

        <input
            type="text"
            name="name"
            value="{{ old('name', $vehicle->name ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >

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

        <input
            type="text"
            name="type"
            placeholder="Truck, Forklift, Crane..."
            value="{{ old('type', $vehicle->type ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >

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

        <input
            type="text"
            name="brand"
            value="{{ old('brand', $vehicle->brand ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >
    </div>


    <div>
        <label class="mb-2 block text-sm font-medium">
            Model
        </label>

        <input
            type="text"
            name="model"
            value="{{ old('model', $vehicle->model ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >
    </div>


    <div>
        <label class="mb-2 block text-sm font-medium">
            Registration Number
        </label>

        <input
            type="text"
            name="registration_number"
            value="{{ old(
                'registration_number',
                $vehicle->registration_number ?? ''
            ) }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >

        @error('registration_number')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label class="mb-2 block text-sm font-medium">
            Status
        </label>

        <select
            name="status"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >
            @foreach($statuses as $status)
                <option
                    value="{{ $status->value }}"
                    @selected(
                        old(
                            'status',
                            isset($vehicle)
                                ? $vehicle->status->value
                                : 'active'
                        ) === $status->value
                    )
                >
                    {{ str($status->value)
                        ->replace('_', ' ')
                        ->title() }}
                </option>
            @endforeach
        </select>

        @error('status')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label class="mb-2 block text-sm font-medium">
            Purchase Date
        </label>

        <input
            type="date"
            name="purchase_date"
            value="{{ old(
                'purchase_date',
                isset($vehicle) && $vehicle->purchase_date
                    ? $vehicle->purchase_date->format('Y-m-d')
                    : ''
            ) }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >

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

    <textarea
        name="notes"
        rows="4"
        class="w-full rounded-lg border border-gray-300 px-3 py-2"
    >{{ old('notes', $vehicle->notes ?? '') }}</textarea>
</div>