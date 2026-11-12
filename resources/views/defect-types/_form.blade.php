
@csrf

@if($defectType->exists)
    @method('PUT')
@endif

<div class="space-y-5">

    {{-- Name --}}
    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
            Defect Type Name <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $defectType->name) }}"
            placeholder="e.g. Hydraulic System"
            required
            maxlength="150"
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                   focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
            Description
        </label>

        <textarea
            id="description"
            name="description"
            rows="4"
            placeholder="Describe this defect category..."
            class="w-full rounded-lg border border-gray-300 px-4 py-3
                   focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200">{{ old('description', $defectType->description) }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Actions --}}
    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">

        <a href="{{ route('defect-types.index') }}"
            class="rounded-lg border border-gray-300 px-5 py-2.5 text-center
                   text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>

        <button type="submit"
            class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm
                   font-medium text-white hover:bg-gray-800">
            {{ $defectType->exists ? 'Save Changes' : 'Create Defect Type' }}
        </button>

    </div>
</div>
