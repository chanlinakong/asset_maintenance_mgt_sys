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

        defects: [],
        selectedDefectTypeId: @js(
            old('selected_defect_type_id', old('new_defects.0.defect_type_id', ''))
        ),

        selectedDefectIds: @js(
            array_map('strval', old('defect_ids', []))
        ),
        loadingDefects: false,

        newDefects: @js(old('new_defects', [])),

        defectTypes: @js(
            ($defectTypes ?? collect())->map(fn($type) => [
                'id' => $type->id,
                'name' => $type->name,
            ])->values()
        ),

        async loadDefects() {
            if (!this.vehicleId) {
                this.defects = [];
                this.selectedDefectIds = [];
                return;
            }

            this.loadingDefects = true;

            try {
                const response = await fetch(
                    `/vehicles/${this.vehicleId}/available-defects`,
                    {
                        headers: {
                            'Accept': 'application/json',
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error('Failed to load vehicle defects.');
                }

                this.defects = await response.json();

                // Select the first defect type automatically when available.
                if (
                    !this.selectedDefectTypeId &&
                    this.defects.length > 0
                ) {
                    this.selectedDefectTypeId = String(this.defects[0].defect_type_id);
                }

            } catch (error) {
                console.error(error);
                this.defects = [];

                alert('Unable to load defects for this vehicle.');

            } finally {
                this.loadingDefects = false;
            }
        },

        addNewDefect() {
            const typeId = this.selectedDefectTypeId || '';

            this.newDefects.push({
                defect_type_id: typeId,
                title: '',
                description: ''
            });
        },

        filteredDefects() {
            if (!this.selectedDefectTypeId) {
                return [];
            }

            return this.defects.filter(defect =>
                String(defect.defect_type_id) === String(this.selectedDefectTypeId)
            );
        },

        filteredNewDefects() {
            if (!this.selectedDefectTypeId) {
                return [];
            }

            return this.newDefects
                .map((defect, index) => ({ ...defect, index }))
                .filter(defect =>
                    String(defect.defect_type_id) === String(this.selectedDefectTypeId)
                );
        },

        get selectedDefectCount() {
            return this.selectedDefectIds.length + this.newDefects.length;
        },

        removeNewDefect(index) {
            this.newDefects.splice(index, 1);
        },

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
            this.selectedDefectTypeId = '';
            this.selectedScheduleId = '';

            // Defects from the previous vehicle must not remain selected.
            this.defects = [];
            this.selectedDefectIds = [];
            this.newDefects = [];

            await Promise.all([
                this.loadSchedules(),
                this.loadDefects(),
            ]);
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
            loadDefects();
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

    {{-- Submit every selected existing defect, including defects
    from categories that are not currently displayed. --}}
    <template x-for="defectId in selectedDefectIds" :key="'selected-defect-' + defectId">
        <input type="hidden" name="defect_ids[]" :value="defectId">
    </template>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        {{-- VEHICLE --}}
        <div>
            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
                Vehicle
            </label>

            <select name="vehicle_id" id="vehicle_id" x-model="vehicleId" @change="vehicleChanged()"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
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
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
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


        @unless($isEdit)
            <div class="col-span-full space-y-5">

                {{-- Defect selection --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    {{-- Header --}}
                    <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    Vehicle Defects
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Select the defects related to this maintenance.
                                </p>
                            </div>

                            <span
                                class="inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700">
                                <span x-text="selectedDefectCount"></span>
                                <span class="ml-1">selected</span>
                            </span>
                        </div>
                    </div>

                    <div class="space-y-5 p-5 sm:p-6">

                        {{-- No vehicle --}}
                        <div x-show="!vehicleId"
                            class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-gray-700">
                                Select a vehicle first
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                Its registered defects will appear here.
                            </p>
                        </div>

                        {{-- Loading --}}
                        <div x-show="vehicleId && loadingDefects"
                            class="flex items-center justify-center gap-3 py-8 text-sm text-gray-500">
                            <svg class="h-5 w-5 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Loading vehicle defects...
                        </div>

                        <div x-show="vehicleId && !loadingDefects">

                            {{-- Defect type dropdown --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Defect Type
                                </label>

                                <select x-model="selectedDefectTypeId"
                                    class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Choose a defect type</option>

                                    <template x-for="defectType in defectTypes" :key="defectType.id">
                                        <option :value="String(defectType.id)" x-text="defectType.name">
                                        </option>
                                    </template>
                                </select>
                            </div>

                            {{-- Defect checklist --}}
                            <div class="mt-5">

                                <div x-show="!selectedDefectTypeId" class="rounded-xl bg-gray-50 px-4 py-8 text-center">
                                    <p class="text-sm text-gray-500">
                                        Choose a defect type to see its registered defects.
                                    </p>
                                </div>

                                <div x-show="selectedDefectTypeId">

                                    <div class="mb-3 flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-800">
                                            Available Defects
                                        </p>

                                        <span class="text-xs text-gray-500"
                                            x-text="filteredDefects().length + ' registered'">
                                        </span>
                                    </div>

                                    <div class="space-y-3">

                                        {{-- Existing defects --}}
                                        <template x-for="defect in filteredDefects()" :key="'existing-' + defect.id">

                                            <label
                                                class="flex cursor-pointer gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-blue-300 hover:bg-blue-50/30">

                                                <input type="checkbox" :value="String(defect.id)"
                                                    x-model="selectedDefectIds"
                                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                                                <div class="min-w-0 flex-1">

                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="text-sm font-semibold text-gray-900"
                                                            x-text="defect.title">
                                                        </span>

                                                        <span
                                                            class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600"
                                                            x-text="defect.occurrences_count + ' occurrences'">
                                                        </span>
                                                    </div>

                                                    <p x-show="defect.description" class="mt-1 text-sm text-gray-500"
                                                        x-text="defect.description">
                                                    </p>

                                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">

                                                        <span class="text-gray-400"
                                                            x-text="'Reported: ' + (defect.reported_at || '—')">
                                                        </span>

                                                        <span x-show="defect.status === 'resolved'"
                                                            class="rounded-full bg-green-50 px-2 py-1 font-medium text-green-700">
                                                            Previously resolved
                                                        </span>

                                                        <span x-show="defect.status === 'in_progress'"
                                                            class="rounded-full bg-blue-50 px-2 py-1 font-medium text-blue-700">
                                                            In progress
                                                        </span>

                                                        <span x-show="defect.status === 'open'"
                                                            class="rounded-full bg-amber-50 px-2 py-1 font-medium text-amber-700">
                                                            Open
                                                        </span>
                                                    </div>

                                                    {{-- Unresolved warning --}}
                                                    <div x-show="defect.has_unresolved_occurrence"
                                                        class="mt-3 flex items-start gap-2 rounded-lg bg-amber-50 p-3 text-xs text-amber-800">

                                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v3m0 4h.01M10.3 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.7 3.86a2 2 0 00-3.4 0z" />
                                                        </svg>

                                                        <span>
                                                            <strong>Already reported:</strong>
                                                            <span x-text="defect.unresolved_occurrences_count"></span>
                                                            unresolved occurrence(s) in active maintenance.
                                                            Check existing reports before adding another.
                                                        </span>
                                                    </div>

                                                </div>
                                            </label>

                                        </template>

                                        {{-- Newly registered defects (not saved yet) --}}
                                        <template x-for="defect in filteredNewDefects()" :key="'new-' + defect.index">

                                            <div class="rounded-xl border border-dashed border-blue-300 bg-blue-50/40 p-4">

                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </span>

                                                        <span class="text-sm font-semibold text-blue-900"
                                                            x-text="defect.title || 'New defect (enter title below)'">
                                                        </span>
                                                    </div>

                                                    <span
                                                        class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                                        New
                                                    </span>
                                                </div>

                                                <p class="mt-2 text-xs text-blue-700">
                                                    This defect will be registered and included when you save this
                                                    maintenance record.
                                                </p>
                                            </div>

                                        </template>

                                        {{-- No defects in this category --}}
                                        <div x-show="filteredDefects().length === 0 && filteredNewDefects().length === 0"
                                            class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center">
                                            <p class="text-sm font-medium text-gray-700">
                                                No defects in this category
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                You can register a new defect below.
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                        @error('defect_ids')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @error('defect_ids.*')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    </div>
                </div>

                {{-- Register a new defect --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">
                                Register a New Defect
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Can't find the defect? Add it to this vehicle.
                            </p>
                        </div>

                        <button type="button" @click="addNewDefect()"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700">

                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>

                            Add Defect
                        </button>
                    </div>

                    <div class="mt-5 space-y-4">

                        <template x-for="(defect, index) in newDefects" :key="index">

                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                                <div class="mb-4 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-800">
                                        New Defect
                                    </span>

                                    <button type="button" @click="removeNewDefect(index)"
                                        class="text-sm font-medium text-red-600 hover:text-red-700">
                                        Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                            Defect Type
                                        </label>

                                        <select x-model="defect.defect_type_id"
                                            @change="selectedDefectTypeId = String(defect.defect_type_id)"
                                            :name="`new_defects[${index}][defect_type_id]`" required
                                            class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                            <option value="">Select type</option>

                                            <template x-for="defectType in defectTypes" :key="defectType.id">
                                                <option :value="defectType.id" x-text="defectType.name">
                                                </option>
                                            </template>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                            Defect Title
                                        </label>

                                        <input type="text" x-model="defect.title" :name="`new_defects[${index}][title]`"
                                            maxlength="255" required placeholder="e.g. Engine overheating"
                                            class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                </div>

                                <div class="mt-4">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                        Description
                                        <span class="font-normal text-gray-400">(Optional)</span>
                                    </label>

                                    <textarea x-model="defect.description" :name="`new_defects[${index}][description]`"
                                        rows="2" placeholder="Describe the problem..."
                                        class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>

                            </div>

                        </template>

                        <div x-show="newDefects.length === 0"
                            class="rounded-xl border border-dashed border-gray-300 px-4 py-6 text-center">
                            <p class="text-sm text-gray-500">
                                No new defects added.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        @endunless


        {{-- TITLE --}}
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">
                Maintenance Title
            </label>

            <input type="text" name="title" id="title" value="{{ old('title', $maintenance?->title ?? '') }}"
                placeholder="e.g. Engine Repair"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
                required>

            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- PREVENTIVE MAINTENANCE SCHEDULE --}}
        <div x-show="type === 'preventive'" x-cloak>

            {{-- No vehicle selected --}}
            <div x-show="!vehicleId" x-cloak class="mt-7 rounded-lg bg-yellow-50 px-3 py-2 text-sm text-yellow-700">
                Please select a vehicle first to view its maintenance schedules.
            </div>

            {{-- Vehicle selected --}}
            <div x-show="vehicleId" x-cloak>
                <label for="maintenance_schedule_id" class="block text-sm font-medium text-gray-700">
                    Maintenance Schedule
                </label>

                <select name="maintenance_schedule_id" id="maintenance_schedule_id" x-model="selectedScheduleId"
                    :required="type === 'preventive' && !!vehicleId"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none">
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
                    class="mt-2 flex flex-wrap items-center justify-between gap-2 rounded-lg border-amber-200 bg-amber-50 px-3 py-2 text-sm">
                    <p class="text-amber-800">
                        No active maintenance schedules for this vehicle.
                    </p>

                    <a :href="'{{ route('maintenance-schedules.create') }}?vehicle_id=' + encodeURIComponent(vehicleId)"
                        class="inline-flex shrink-0 items-center gap-1 font-medium text-indigo-700 transition-colors hover:text-indigo-900 hover:underline">
                        Create Schedule
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
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none">

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
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
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
) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
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
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none">

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
) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none">

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
) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none">

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
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
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
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
                        placeholder="0.00">


                    @error('other_cost')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>


            {{-- PARTS INFORMATION --}}

            <!-- <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 p-4">

                                                                    <p class="text-sm font-medium text-blue-900">
                                                                        Parts Cost
                                                                    </p>

                                                                    <p class="mt-1 text-sm text-blue-700">
                                                                        Parts are not entered manually here. Add the actual parts
                                                                        used after saving the maintenance record.
                                                                    </p>

                                                                </div> -->

        </div>
    @endif

    {{-- DESCRIPTION --}}

    <div class="mt-5">

        <label for="description" class="block text-sm font-medium text-gray-700">
            Description
        </label>

        <textarea name="description" id="description" rows="4" placeholder="Describe the maintenance work..."
            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none">{{ old(
    'description',
    $maintenance?->description ?? ''
) }}
        </textarea>


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
            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none">{{ old(
    'notes',
    $maintenance?->notes ?? ''
) }}
        </textarea>


        @error('notes')

            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>

        @enderror

    </div>

</div>