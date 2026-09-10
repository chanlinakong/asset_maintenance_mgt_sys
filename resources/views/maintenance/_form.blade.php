<div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    {{-- Vehicle --}}
    <div>
        <label class="mb-2 block text-sm font-medium">
            Vehicle
        </label>

        <select name="vehicle_id" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="">
                Select vehicle
            </option>

            @php
                $maintenance = $maintenance ?? null;
            @endphp

            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" @selected(
                    old(
                        'vehicle_id',
                        data_get($maintenance, 'vehicle_id', '')
                    ) == $vehicle->id
                )>
                    {{ $vehicle->vehicle_code }}
                    — {{ $vehicle->name }}
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
        <label class="mb-2 block text-sm font-medium">
            Maintenance Title
        </label>

        <input type="text" name="title" value="{{ old('title', data_get($maintenance, 'title', '')) }}"
            placeholder="e.g. Engine Repair" class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('title')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Type --}}
    <div>
        <label class="mb-2 block text-sm font-medium">
            Type
        </label>

        <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            @foreach($types as $type)
                    <option value="{{ $type->value }}" @selected(
                        old(
                            'type',
                            data_get($maintenance, 'type.value', 'corrective')
                        ) === $type->value
                    )>
                        {{ str($type->value)
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


    {{-- Status --}}
    <div>
        <label class="mb-2 block text-sm font-medium">
            Status
        </label>

        <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(
                        old(
                            'status',
                            data_get($maintenance, 'status.value', 'pending')
                        ) === $status->value
                    )>
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


    {{-- Reported --}}
    <div>
        <label class="mb-2 block text-sm font-medium">
            Reported At
        </label>

        <input type="datetime-local" name="reported_at" value="{{ old(
    'reported_at',
    isset($maintenance)
    ? data_get($maintenance, 'reported_at')->format('Y-m-d\TH:i')
    : now()->format('Y-m-d\TH:i')
) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('reported_at')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Started --}}
    <div>
        <label class="mb-2 block text-sm font-medium">
            Started At
        </label>

        <input type="datetime-local" name="started_at" value="{{ old(
    'started_at',
    isset($maintenance) && data_get($maintenance, 'started_at')
    ? data_get($maintenance, 'started_at')->format('Y-m-d\TH:i')
    : ''
) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('started_at')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Completed --}}
    <div>
        <label class="mb-2 block text-sm font-medium">
            Completed At
        </label>

        <input type="datetime-local" name="completed_at" value="{{ old(
    'completed_at',
    isset($maintenance) && data_get($maintenance, 'completed_at')
    ? data_get($maintenance, 'completed_at')->format('Y-m-d\TH:i')
    : ''
) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('completed_at')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Cost --}}
    <div>
        <label class="mb-2 block text-sm font-medium">
            Cost
        </label>

        <input type="number" name="cost" step="0.01" min="0"
            value="{{ old('cost', data_get($maintenance, 'cost', '0.00')) }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2">

        @error('cost')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Service Provider --}}
    <div>
        <label class="mb-2 block text-sm font-medium">
            Service Provider
        </label>

        <input type="text" name="service_provider" value="{{ old(
    'service_provider',
    data_get($maintenance, 'service_provider', '')
) }}" placeholder="Workshop / Company" class="w-full rounded-lg border border-gray-300 px-3 py-2">
    </div>

</div>


{{-- Description --}}
<div class="mt-5">

    <label class="mb-2 block text-sm font-medium">
        Description
    </label>

    <textarea name="description" rows="4"
        class="w-full rounded-lg border border-gray-300 px-3 py-2">{{ old('description', data_get($maintenance, 'description', '')) }}</textarea>

</div>


{{-- Notes --}}
<div class="mt-5">

    <label class="mb-2 block text-sm font-medium">
        Notes
    </label>

    <textarea name="notes" rows="4"
        class="w-full rounded-lg border border-gray-300 px-3 py-2">{{ old('notes', data_get($maintenance, 'notes', '')) }}</textarea>

</div>