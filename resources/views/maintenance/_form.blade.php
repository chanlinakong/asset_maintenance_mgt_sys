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
        selectedDefectIds: [],
        loadingDefects: false,

        newDefects: [],

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

            } catch (error) {
                console.error(error);
                this.defects = [];

                alert('Unable to load defects for this vehicle.');

            } finally {
                this.loadingDefects = false;
            }
        },

        addNewDefect() {
            this.newDefects.push({
                defect_type_id: '',
                title: '',
                description: '',
            });
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
            {{-- EXISTING VEHICLE DEFECTS --}}
            <div class="md:col-span-2 rounded-xl border border-gray-200 bg-white p-5">

                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">
                            Existing Vehicle Defects
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Select the unresolved defects that this maintenance report will handle.
                        </p>
                    </div>

                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700"
                        x-text="selectedDefectIds.length + ' selected'"></span>
                </div>

                {{-- No vehicle --}}
                <div x-show="!vehicleId" x-cloak class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-500">
                    Select a vehicle first to view its defects.
                </div>

                {{-- Loading --}}
                <div x-show="vehicleId && loadingDefects" x-cloak class="mt-4 text-sm text-gray-500">
                    Loading vehicle defects...
                </div>

                {{-- Defect list --}}
                <div x-show="vehicleId && !loadingDefects && defects.length > 0" x-cloak class="mt-4 space-y-3">
                    <template x-for="defect in defects" :key="defect.id">
                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-4 transition hover:border-indigo-300 hover:bg-indigo-50/30">
                            <input type="checkbox" name="defect_ids[]" :value="defect.id" x-model="selectedDefectIds"
                                class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-medium text-gray-900" x-text="defect.title"></span>

                                    <span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700"
                                        x-text="defect.status.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())"></span>
                                </div>

                                <p class="mt-1 text-sm text-gray-500" x-text="defect.defect_type || 'Uncategorized'"></p>

                                <p x-show="defect.description" class="mt-1 text-sm text-gray-600"
                                    x-text="defect.description"></p>

                                <p class="mt-2 text-xs text-gray-400"
                                    x-text="'Reported: ' + (defect.reported_at || 'Unknown')"></p>
                            </div>
                        </label>
                    </template>
                </div>

                {{-- No defects --}}
                <div x-show="vehicleId && !loadingDefects && defects.length === 0" x-cloak
                    class="mt-4 rounded-lg border border-dashed border-gray-300 p-5 text-center">
                    <p class="text-sm font-medium text-gray-700">
                        No available unresolved defects
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        You can register a new defect below.
                    </p>
                </div>

                @error('defect_ids')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @error('defect_ids.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

            </div>

            {{-- REGISTER NEW DEFECTS --}}
            <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-5">

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">
                            Register Additional Defects
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Add defects discovered during inspection or maintenance reporting.
                        </p>
                    </div>

                    <button type="button" @click="addNewDefect()" :disabled="!vehicleId"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                        <span aria-hidden="true">+</span>
                        Add Defect
                    </button>
                </div>

                <div x-show="!vehicleId" x-cloak class="mt-4 text-sm text-gray-500">
                    Select a vehicle before registering defects.
                </div>

                <div x-show="vehicleId && newDefects.length === 0" x-cloak
                    class="mt-4 rounded-lg border border-dashed border-gray-300 bg-white p-4 text-center text-sm text-gray-500">
                    No new defects added.
                </div>

                <div class="mt-4 space-y-4">

                    <template x-for="(defect, index) in newDefects" :key="index">

                        <div class="rounded-lg border border-gray-200 bg-white p-4">

                            <div class="mb-4 flex items-center justify-between">
                                <h4 class="font-medium text-gray-800" x-text="'New Defect ' + (index + 1)"></h4>

                                <button type="button" @click="removeNewDefect(index)"
                                    class="text-sm font-medium text-red-600 hover:text-red-800">
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                {{-- DEFECT TYPE --}}
                                <div>
                                    <label :for="'new-defect-type-' + index"
                                        class="block text-sm font-medium text-gray-700">
                                        Defect Type
                                    </label>

                                    <select :id="'new-defect-type-' + index"
                                        :name="'new_defects[' + index + '][defect_type_id]'" x-model="defect.defect_type_id"
                                        required
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
                                        <option value="">Select defect type</option>

                                        <template x-for="type in defectTypes" :key="type.id">
                                            <option :value="type.id" x-text="type.name"></option>
                                        </template>
                                    </select>

                                    @error('new_defects.*.defect_type_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- DEFECT TITLE --}}
                                <div>
                                    <label :for="'new-defect-title-' + index"
                                        class="block text-sm font-medium text-gray-700">
                                        Defect Title
                                    </label>

                                    <input type="text" :id="'new-defect-title-' + index"
                                        :name="'new_defects[' + index + '][title]'" x-model="defect.title" maxlength="200"
                                        required placeholder="e.g. Engine overheating"
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">

                                    @error('new_defects.*.title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- DESCRIPTION --}}
                                <div class="md:col-span-2">
                                    <label :for="'new-defect-description-' + index"
                                        class="block text-sm font-medium text-gray-700">
                                        Description
                                    </label>

                                    <textarea :id="'new-defect-description-' + index"
                                        :name="'new_defects[' + index + '][description]'" x-model="defect.description"
                                        rows="2" placeholder="Describe the defect..."
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none"></textarea>
                                </div>

                            </div>

                        </div>

                    </template>

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