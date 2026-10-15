@php
    $maintenance = $maintenance ?? null;

    $isEdit = $maintenance !== null;

    $selectedVehicleId = old(
        'vehicle_id',
        $maintenance?->vehicle_id ?? request('vehicle_id')
    );

    $selectedType = old(
        'type',
        $maintenance?->type?->value
    );


    $selectedStatus = old(
        'status',
        $maintenance?->status?->value ?? 'pending'
    );

    $selectedScheduleId = old(
        'maintenance_schedule_id',
        $maintenance?->maintenance_schedule_id
    );

    $selectedServiceKilometers = old(
        'service_kilometers',
        $maintenance?->service_kilometers
    );

    $selectedLaborCost = old(
        'labor_cost',
        $maintenance?->labor_cost ?? 0
    );

    $selectedOtherCost = old(
        'other_cost',
        $maintenance?->other_cost ?? 0
    );
@endphp


<div x-data="{
        vehicleId: @js($selectedVehicleId),

        type: @js($selectedType),

        status: @js($selectedStatus),

        selectedScheduleId: @js($selectedScheduleId),

        schedules: @js($schedules ?? []),

        loadingSchedules: false,

        async loadSchedules() {

            /*
            |--------------------------------------------------------------------------
            | No vehicle selected
            |--------------------------------------------------------------------------
            */

            if (!this.vehicleId) {
                this.schedules = [];
                this.selectedScheduleId = '';
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
                    throw new Error(
                        'Failed to load maintenance schedules.'
                    );
                }


                this.schedules = await response.json();


                /*
                |--------------------------------------------------------------------------
                | Make sure currently selected schedule still belongs
                | to the selected vehicle.
                |--------------------------------------------------------------------------
                */

                const scheduleExists = this.schedules.some(
                    schedule =>
                        String(schedule.id)
                        === String(this.selectedScheduleId)
                );


                if (!scheduleExists) {
                    this.selectedScheduleId = '';
                }

            } catch (error) {

                console.error(error);

                this.schedules = [];

                this.selectedScheduleId = '';

            } finally {

                this.loadingSchedules = false;

            }
        },


        /*
        |--------------------------------------------------------------------------
        | Vehicle changed
        |--------------------------------------------------------------------------
        */

        async vehicleChanged() {

            this.selectedScheduleId = '';

            await this.loadSchedules();

        },


        /*
        |--------------------------------------------------------------------------
        | Type changed
        |--------------------------------------------------------------------------
        */

        typeChanged() {

            /*
            | A schedule is only valid for Preventive maintenance.
            */

            if (this.type !== 'preventive') {
                this.selectedScheduleId = '';
            }

        }

    }" x-init="
        if (vehicleId) {
            loadSchedules();
        }
    ">

    {{-- MAIN FORM GRID --}}

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="font-medium text-red-800">
                Please correct the following errors:
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        {{-- VEHICLE --}}
        <div>
            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
                Vehicle
            </label>

            <select name="vehicle_id" id="vehicle_id" x-model="vehicleId" @change="vehicleChanged()"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                required>
                <option value="">Select vehicle</option>

                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}">
                        {{ $vehicle->vehicle_code }} — {{ $vehicle->name }}
                    </option>
                @endforeach
            </select>

            @error('vehicle_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- MAINTENANCE TYPE --}}
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700">
                Maintenance Type
            </label>

            <select name="type" id="type" x-model="type" @change="typeChanged()"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                required>
                <option value="">Select maintenance type</option>

                @foreach(\App\Enums\MaintenanceType::cases() as $maintenanceType)
                    <option value="{{ $maintenanceType->value }}">
                        {{ str($maintenanceType->value)->replace('_', ' ')->title() }}
                    </option>
                @endforeach
            </select>

            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- TITLE --}}
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">
                Maintenance Title
            </label>

            <input type="text" name="title" id="title" value="{{ old('title', $maintenance?->title ?? '') }}"
                placeholder="e.g. Engine Repair"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                required>

            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- PREVENTIVE MAINTENANCE SCHEDULE --}}
        <div x-show="type === 'preventive'" x-cloak>

            {{-- No vehicle selected --}}
            <div x-show="!vehicleId" x-cloak class="mt-2 rounded-lg bg-yellow-50 p-3 text-sm text-yellow-700">
                Please select a vehicle first to view its maintenance schedules.
            </div>

            {{-- Vehicle selected --}}
            <div x-show="vehicleId" x-cloak>
                <label for="maintenance_schedule_id" class="block text-sm font-medium text-gray-700">
                    Maintenance Schedule
                </label>

                <select name="maintenance_schedule_id" id="maintenance_schedule_id" x-model="selectedScheduleId"
                    :required="type === 'preventive' && !!vehicleId"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                    <option value="">Select preventive maintenance schedule</option>

                    <template x-for="schedule in schedules" :key="schedule.id">
                        <option :value="schedule.id" x-text="schedule.title"></option>
                    </template>
                </select>

                @error('maintenance_schedule_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Loading --}}
                <div x-show="loadingSchedules" x-cloak class="mt-2 text-sm text-gray-500">
                    Loading maintenance schedules...
                </div>

                {{-- No schedules --}}
                <div x-show="!loadingSchedules && vehicleId && schedules.length === 0" x-cloak
                    class="mt-2 rounded-lg bg-yellow-50 p-3 text-sm text-yellow-700">
                    <p>
                        This vehicle has no active preventive maintenance schedules.
                    </p>

                    <a :href="'{{ route('maintenance-schedules.create') }}?vehicle_id=' + encodeURIComponent(vehicleId)"
                        class="mt-2 inline-flex items-center gap-1 font-medium text-indigo-700 hover:text-indigo-900 hover:underline">
                        Create Maintenance Schedule
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>

            </div>
        </div>

        {{-- SERVICE KILOMETERS --}}
        <div>
            <label for="service_kilometers" class="block text-sm font-medium text-gray-700">
                Service Kilometers
            </label>

            <input type="number" name="service_kilometers" id="service_kilometers"
                value="{{ $selectedServiceKilometers }}" min="0" step="1" placeholder="e.g. 55200"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">

            @error('service_kilometers')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <p class="mt-1 text-xs text-gray-500">
                Odometer reading when this maintenance was performed.
            </p>
        </div>

        {{-- STATUS --}}
        @if($isEdit)
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">
                    Status
                </label>

                <select name="status" id="status" x-model="status"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                    required>
                    @foreach($statuses as $statusOption)
                        <option value="{{ $statusOption->value }}">
                            {{ str($statusOption->value)->replace('_', ' ')->title() }}
                        </option>
                    @endforeach
                </select>

                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @else
            {{-- New records always start as Pending. --}}
        @endif

        {{-- REPORTED AT --}}
        <div>
            <label for="reported_at" class="block text-sm font-medium text-gray-700">
                Reported At
            </label>

            <input type="datetime-local" name="reported_at" id="reported_at" value="{{ old(
    'reported_at',
    $maintenance?->reported_at
    ? $maintenance->reported_at->format('Y-m-d\TH:i')
    : now()->format('Y-m-d\TH:i')
) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                required>

            @error('reported_at')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- SERVICE PROVIDER --}}
        <div>
            <label for="service_provider" class="block text-sm font-medium text-gray-700">
                Service Provider
            </label>

            <input type="text" name="service_provider" id="service_provider"
                value="{{ old('service_provider', $maintenance?->service_provider ?? '') }}"
                placeholder="Workshop / Company"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">

            @error('service_provider')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- STARTED AT --}}
        <template x-if="status === 'in_progress' || status === 'completed'">
            <div>
                <label for="started_at" class="block text-sm font-medium text-gray-700">
                    Started At
                </label>

                <input type="datetime-local" name="started_at" id="started_at" value="{{ old(
    'started_at',
    $maintenance?->started_at
    ? $maintenance->started_at->format('Y-m-d\TH:i')
    : ''
) }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">

                @error('started_at')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </template>

        {{-- COMPLETED AT --}}
        <template x-if="status === 'completed'">
            <div>
                <label for="completed_at" class="block text-sm font-medium text-gray-700">
                    Completed At
                </label>

                <input type="datetime-local" name="completed_at" id="completed_at" value="{{ old(
    'completed_at',
    $maintenance?->completed_at
    ? $maintenance->completed_at->format('Y-m-d\TH:i')
    : ''
) }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">

                @error('completed_at')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </template>

    </div>

    {{-- COST --}}

    @if($isEdit)
        <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-5">

            <h3 class="text-base font-semibold text-gray-900">
                Cost
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Labor and other costs are entered here. Parts are added
                separately from the maintenance details page.
            </p>


            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">


                {{-- LABOR COST --}}

                <div>

                    <label for="labor_cost" class="block text-sm font-medium text-gray-700">
                        Labor Cost
                    </label>

                    <input type="number" name="labor_cost" id="labor_cost" value="{{ $selectedLaborCost }}" min="0"
                        step="0.01"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                        placeholder="0.00">


                    @error('labor_cost')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- OTHER COST --}}

                <div>

                    <label for="other_cost" class="block text-sm font-medium text-gray-700">
                        Other Cost
                    </label>

                    <input type="number" name="other_cost" id="other_cost" value="{{ $selectedOtherCost }}" min="0"
                        step="0.01"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                        placeholder="0.00">


                    @error('other_cost')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>


            {{-- PARTS INFORMATION --}}

            <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 p-4">

                <p class="text-sm font-medium text-blue-900">
                    Parts Cost
                </p>

                <p class="mt-1 text-sm text-blue-700">
                    Parts are not entered manually here. Add the actual parts
                    used after saving the maintenance record.
                </p>

            </div>

        </div>
    @endif

    {{-- DESCRIPTION --}}

    <div class="mt-5">

        <label for="description" class="block text-sm font-medium text-gray-700">
            Description
        </label>

        <textarea name="description" id="description" rows="4" placeholder="Describe the maintenance work..."
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">{{ old(
    'description',
    $maintenance?->description ?? ''
) }}</textarea>


        @error('description')

            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>

        @enderror

    </div>


    {{-- NOTES --}}

    <div class="mt-5">

        <label for="notes" class="block text-sm font-medium text-gray-700">
            Notes
        </label>

        <textarea name="notes" id="notes" rows="4" placeholder="Additional notes..."
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">{{ old(
    'notes',
    $maintenance?->notes ?? ''
) }}</textarea>


        @error('notes')

            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>

        @enderror

    </div>

</div>