{{-- Vehicle Information --}}
<div class="space-y-6">

    <div>
        <h2 class="text-base font-semibold text-gray-800">
            Vehicle Information
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Enter the basic information and specifications of this vehicle.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">

        {{-- Vehicle Code --}}
        <div>
            <label for="vehicle_code" class="mb-2 block text-sm font-medium text-gray-700">
                Vehicle Code
            </label>

            <input
                type="text"
                name="vehicle_code"
                id="vehicle_code"
                value="{{ old('vehicle_code', $vehicle->vehicle_code ?? '') }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            @error('vehicle_code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Vehicle Name --}}
        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                Vehicle Name
            </label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $vehicle->name ?? '') }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Type --}}
        <div>
            <label for="type" class="mb-2 block text-sm font-medium text-gray-700">
                Type
            </label>

            <input
                type="text"
                name="type"
                id="type"
                placeholder="Truck, Forklift, Crane..."
                value="{{ old('type', $vehicle->type ?? '') }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Brand --}}
        <div>
            <label for="brand" class="mb-2 block text-sm font-medium text-gray-700">
                Brand
            </label>

            <input
                type="text"
                name="brand"
                id="brand"
                value="{{ old('brand', $vehicle->brand ?? '') }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            @error('brand')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Model --}}
        <div>
            <label for="model" class="mb-2 block text-sm font-medium text-gray-700">
                Model
            </label>

            <input
                type="text"
                name="model"
                id="model"
                value="{{ old('model', $vehicle->model ?? '') }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            @error('model')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Registration Number --}}
        <div>
            <label for="registration_number" class="mb-2 block text-sm font-medium text-gray-700">
                Registration Number
            </label>

            <input
                type="text"
                name="registration_number"
                id="registration_number"
                value="{{ old('registration_number', $vehicle->registration_number ?? '') }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            @error('registration_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>

{{-- Operational Information --}}
<div class="mt-8 border-t border-gray-100 pt-6">

    <div class="mb-5">
        <h2 class="text-base font-semibold text-gray-800">
            Operational Information
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Update the vehicle's current usage, status and purchase information.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">

        {{-- Current Kilometers --}}
        <div>
            <label for="current_kilometers" class="mb-2 block text-sm font-medium text-gray-700">
                Current Kilometers
            </label>

            <input
                type="number"
                name="current_kilometers"
                id="current_kilometers"
                value="{{ old('current_kilometers', $vehicle->current_kilometers) }}"
                min="0"
                step="1"
                placeholder="e.g. 50000"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            <p class="mt-1 text-xs text-gray-500">
                Enter the vehicle's current odometer reading.
            </p>

            @error('current_kilometers')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Purchase Date --}}
        <div>
            <label for="purchase_date" class="mb-2 block text-sm font-medium text-gray-700">
                Purchase Date
            </label>

            <input
                type="date"
                name="purchase_date"
                id="purchase_date"
                value="{{ old('purchase_date', $vehicle->purchase_date?->format('Y-m-d')) }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            @error('purchase_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        @if ($vehicle->exists)
            <div class="md:col-span-2">

                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Status
                </label>

                @if ($vehicle->status !== \App\Enums\VehicleStatus::Maintenance)

                    <div class="flex min-h-[42px] items-center rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">

                        <input type="hidden" name="status" value="active">

                        <label class="flex cursor-pointer items-center gap-3">
                            <input
                                type="checkbox"
                                name="status"
                                value="out_of_service"
                                @checked(old('status', $vehicle->status?->value) === 'out_of_service')
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >

                            <span class="text-sm text-gray-700">
                                Out of Service
                            </span>
                        </label>

                    </div>

                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                @else

                    <div class="flex min-h-[42px] items-center rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                        <span class="text-sm font-medium text-amber-700">
                            Maintenance
                        </span>

                        <span class="ml-2 text-xs text-amber-600">
                            (Managed by active maintenance record)
                        </span>
                    </div>

                @endif
            </div>
        @endif

    </div>
</div>

{{-- Additional Information --}}
<div class="mt-8 border-t border-gray-100 pt-6">

    <div class="mb-5">
        <h2 class="text-base font-semibold text-gray-800">
            Additional Information
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Add notes, documents and pictures related to this vehicle.
        </p>
    </div>

    <div class="grid grid-cols-1 items-stretch gap-5 md:grid-cols-2">

        {{-- Notes --}}
        <div class="flex flex-col">
            <label for="notes" class="mb-2 block text-sm font-medium text-gray-700">
                Notes
            </label>

            <textarea
                name="notes"
                id="notes"
                rows="6"
                placeholder="Additional vehicle information..."
                class="w-full flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >{{ old('notes', $vehicle->notes ?? '') }}</textarea>

            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Attachments --}}
        <div class="flex flex-col">
            <label class="mb-2 block text-sm font-medium text-gray-700">
                Vehicle Attachments
            </label>

            @if ($vehicle->exists)

                <div class="flex min-h-[160px] flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 p-5 text-center">

                    <div class="mb-3 rounded-full bg-blue-100 p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M12 16V4m0 0L7 9m5-5l5 5M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3" />
                        </svg>
                    </div>

                    <p class="text-sm font-medium text-gray-700">
                        Upload vehicle documents and pictures
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        PDF, DOC, DOCX, JPG, JPEG, PNG, WEBP
                    </p>

                    <button
                        type="button"
                        @click="$dispatch('open-vehicle-uploads')"
                        class="mt-3 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 focus:outline-none"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>

                        Upload files
                    </button>

                </div>

                <p class="mt-2 text-xs text-gray-500">
                    Images: maximum 5 MB. PDF and Word documents: maximum 10 MB per file.
                </p>

                @error('attachments')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

            @else

                <div class="flex min-h-[160px] flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 p-5 text-center">

                    <svg class="mb-2 h-7 w-7 text-gray-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    <p class="text-sm font-medium text-gray-700">
                        Save the vehicle first
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        You can upload documents and pictures after creating the vehicle.
                    </p>

                </div>

            @endif
        </div>

    </div>
</div>
