
@php
    $maintenance = $maintenance ?? null;

    $selectedVehicleId = old(
        'vehicle_id',
        $maintenance?->vehicle_id
    );

    $selectedType = old(
        'type',
        $maintenance?->type?->value
    );

    $selectedStatus = old(
        'status',
        $maintenance?->status?->value ?? 'pending'
    );
@endphp

<div
    x-data="{
        vehicleId: @js($selectedVehicleId),
        type: @js($selectedType),
        status: @js($selectedStatus),
        schedules: @js($schedules ?? []),
        loadingSchedules: false,

        async loadSchedules() {
            if (!this.vehicleId) {
                this.schedules = [];
                return;
            }

            this.loadingSchedules = true;

            try {
                const response = await fetch(
                    `/vehicles/${this.vehicleId}/maintenance-schedules`,
                    {
                        headers: {
                            'Accept': 'application/json',
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error('Failed to load schedules.');
                }

                this.schedules = await response.json();

            } catch (error) {
                console.error(error);
                this.schedules = [];

            } finally {
                this.loadingSchedules = false;
            }
        }
    }"
    x-init="if (vehicleId) loadSchedules()"
>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        {{-- Vehicle --}}
        <div>
            <label
                for="vehicle_id"
                class="block text-sm font-medium text-gray-700"
            >
                Vehicle
            </label>

            <select
                name="vehicle_id"
                id="vehicle_id"
                x-model="vehicleId"
                @change="loadSchedules()"
                class="mt-1 block w-full rounded-lg border-gray-300"
                required
            >
                <option value="">
                    Select vehicle
                </option>

                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}">
                        {{ $vehicle->vehicle_code }} - {{ $vehicle->name }}
                    </option>
                @endforeach
            </select>

            @error('vehicle_id')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>


        {{-- Title --}}
        <div>
            <label
                for="title"
                class="mb-2 block text-sm font-medium"
            >
                Maintenance Title
            </label>

            <input
                type="text"
                name="title"
                id="title"
                value="{{ old('title', data_get($maintenance, 'title', '')) }}"
                placeholder="e.g. Engine Repair"
                class="w-full rounded-lg border border-gray-300 px-3 py-2"
                required
            >

            @error('title')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>


        {{-- Type --}}
        <div>
            <label
                for="type"
                class="block text-sm font-medium text-gray-700"
            >
                Maintenance Type
            </label>

            <select
                name="type"
                id="type"
                x-model="type"
                class="mt-1 block w-full rounded-lg border-gray-300"
                required
            >
                <option value="">
                    Select maintenance type
                </option>

                @foreach(\App\Enums\MaintenanceType::cases() as $maintenanceType)
                    <option value="{{ $maintenanceType->value }}">
                        {{ str($maintenanceType->value)
                            ->replace('_', ' ')
                            ->title() }}
                    </option>
                @endforeach
            </select>

            @error('type')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>


        {{-- Maintenance Schedule --}}
        <div
            x-show="type === 'preventive'"
            x-cloak
        >
            <label
                for="maintenance_schedule_id"
                class="block text-sm font-medium text-gray-700"
            >
                Maintenance Schedule
            </label>

            <select
                name="maintenance_schedule_id"
                id="maintenance_schedule_id"
                class="mt-1 block w-full rounded-lg border-gray-300"
                :required="type === 'preventive'"
            >
                <option value="">
                    Select preventive maintenance schedule
                </option>

                <template
                    x-for="schedule in schedules"
                    :key="schedule.id"
                >
                    <option
                        :value="schedule.id"
                        x-text="schedule.title"
                    ></option>
                </template>
            </select>

            @error('maintenance_schedule_id')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

            <div
                x-show="loadingSchedules"
                class="mt-2 text-sm text-gray-500"
            >
                Loading maintenance schedules...
            </div>

            <div
                x-show="
                    !loadingSchedules &&
                    type === 'preventive' &&
                    schedules.length === 0
                "
                class="mt-2 rounded-lg bg-yellow-50 p-3 text-sm text-yellow-700"
            >
                This vehicle has no active preventive maintenance schedules.
            </div>
        </div>


        {{-- Service Kilometers --}}
        <div>
            <label
                for="service_kilometers"
                class="block text-sm font-medium text-gray-700"
            >
                Vehicle Kilometer Reading
            </label>

            <input
                type="number"
                name="service_kilometers"
                id="service_kilometers"
                min="0"
                value="{{ old('service_kilometers', $maintenance?->service_kilometers) }}"
                placeholder="e.g. 50000"
                class="mt-1 block w-full rounded-lg border-gray-300"
            >

            <p class="mt-1 text-xs text-gray-500">
                Enter the vehicle's odometer reading when maintenance is performed.
            </p>

            @error('service_kilometers')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>


        {{-- Status --}}
        <div>
            <label
                for="status"
                class="block text-sm font-medium text-gray-700"
            >
                Status
            </label>

            <select
                name="status"
                id="status"
                x-model="status"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                required
            >
                @foreach($statuses as $statusOption)
                    <option
                        value="{{ $statusOption->value }}"
                        @selected(
                            old(
                                'status',
                                $maintenance?->status?->value ?? 'pending'
                            ) === $statusOption->value
                        )
                    >
                        {{ str($statusOption->value)
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


        {{-- Reported At --}}
        <div>
            <label
                for="reported_at"
                class="mb-2 block text-sm font-medium"
            >
                Reported At
            </label>

            <input
                type="datetime-local"
                name="reported_at"
                id="reported_at"
                value="{{ old(
                    'reported_at',
                    $maintenance?->reported_at
                        ? $maintenance->reported_at->format('Y-m-d\TH:i')
                        : now()->format('Y-m-d\TH:i')
                ) }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2"
                required
            >

            @error('reported_at')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>


        {{-- Started At --}}
        <div
            x-show="status === 'in_progress' || status === 'completed'"
            x-transition
        >
            <label
                for="started_at"
                class="mb-2 block text-sm font-medium"
            >
                Started At
            </label>

            <input
                type="datetime-local"
                name="started_at"
                id="started_at"
                value="{{ old(
                    'started_at',
                    $maintenance?->started_at
                        ? $maintenance->started_at->format('Y-m-d\TH:i')
                        : ''
                ) }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2"
            >

            @error('started_at')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>


        {{-- Completed At --}}
        <div
            x-show="status === 'completed'"
            x-transition
        >
            <label
                for="completed_at"
                class="mb-2 block text-sm font-medium"
            >
                Completed At
            </label>

            <input
                type="datetime-local"
                name="completed_at"
                id="completed_at"
                value="{{ old(
                    'completed_at',
                    $maintenance?->completed_at
                        ? $maintenance->completed_at->format('Y-m-d\TH:i')
                        : ''
                ) }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2"
            >

            @error('completed_at')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>


        {{-- Cost --}}
        <div>
            <label
                for="cost"
                class="mb-2 block text-sm font-medium"
            >
                Cost
            </label>

            <input
                type="number"
                name="cost"
                id="cost"
                step="0.01"
                min="0"
                value="{{ old('cost', data_get($maintenance, 'cost', '0.00')) }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2"
            >

            @error('cost')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>


        {{-- Service Provider --}}
        <div>
            <label
                for="service_provider"
                class="mb-2 block text-sm font-medium"
            >
                Service Provider
            </label>

            <input
                type="text"
                name="service_provider"
                id="service_provider"
                value="{{ old(
                    'service_provider',
                    data_get($maintenance, 'service_provider', '')
                ) }}"
                placeholder="Workshop / Company"
                class="w-full rounded-lg border border-gray-300 px-3 py-2"
            >

            @error('service_provider')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

    </div>


    {{-- Description --}}
    <div class="mt-5">
        <label
            for="description"
            class="mb-2 block text-sm font-medium"
        >
            Description
        </label>

        <textarea
            name="description"
            id="description"
            rows="4"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >{{ old('description', data_get($maintenance, 'description', '')) }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Notes --}}
    <div class="mt-5">
        <label
            for="notes"
            class="mb-2 block text-sm font-medium"
        >
            Notes
        </label>

        <textarea
            name="notes"
            id="notes"
            rows="4"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >{{ old('notes', data_get($maintenance, 'notes', '')) }}</textarea>

        @error('notes')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

</div>
