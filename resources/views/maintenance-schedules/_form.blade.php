@php
    $maintenanceSchedule = $maintenanceSchedule ?? null;
@endphp

<div class="space-y-6">

    <div>
        <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
            Vehicle
        </label>

        <select name="vehicle_id" id="vehicle_id" class="mt-1 block w-full rounded-lg border-gray-300" required>
            <option value="">
                Select vehicle
            </option>

            @foreach($vehicles as $vehicle)

                <option value="{{ $vehicle->id }}" @selected(
                    old(
                        'vehicle_id',
                        $maintenanceSchedule?->vehicle_id
                    ) == $vehicle->id
                )>
                    {{ $vehicle->vehicle_code }}
                    -
                    {{ $vehicle->name }}
                </option>

            @endforeach

        </select>

        @error('vehicle_id')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">
            Maintenance Task
        </label>

        <input type="text" name="title" id="title" value="{{ old('title', $maintenanceSchedule?->title) }}"
            placeholder="e.g. Engine Oil Change" class="mt-1 block w-full rounded-lg border-gray-300" required>

        @error('title')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">
            Description
        </label>

        <textarea name="description" id="description" rows="4"
            class="mt-1 block w-full rounded-lg border-gray-300">{{ old('description', $maintenanceSchedule?->description) }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="last_service_date" class="block text-sm font-medium text-gray-700">
            Last Service Date
        </label>

        <input type="date" name="last_service_date" id="last_service_date" value="{{ old(
    'last_service_date',
    $maintenanceSchedule?->last_service_date?->format('Y-m-d')
) }}" class="mt-1 block w-full rounded-lg border-gray-300">

        <p class="mt-1 text-xs text-gray-500">
            Leave empty if this vehicle has never received this service.
        </p>

        @error('last_service_date')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

        <div>
            <label for="interval_days" class="block text-sm font-medium text-gray-700">
                Every X Days
            </label>

            <input type="number" name="interval_days" id="interval_days" min="1" value="{{ old(
    'interval_days',
    $maintenanceSchedule?->interval_days
) }}" placeholder="e.g. 90" class="mt-1 block w-full rounded-lg border-gray-300">

            <p class="mt-1 text-xs text-gray-500">
                Leave empty if this schedule is not time-based.
            </p>

            @error('interval_days')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="interval_kilometers" class="block text-sm font-medium text-gray-700">
                Every X Kilometers
            </label>

            <input type="number" name="interval_kilometers" id="interval_kilometers" min="1" value="{{ old(
    'interval_kilometers',
    $maintenanceSchedule?->interval_kilometers
) }}" placeholder="e.g. 5000" class="mt-1 block w-full rounded-lg border-gray-300">

            <p class="mt-1 text-xs text-gray-500">
                Leave empty if this schedule is not kilometer-based.
            </p>

            @error('interval_kilometers')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

    </div>

</div>