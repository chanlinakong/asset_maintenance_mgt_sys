
@php
    $isEdit = isset($part);

    $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition duration-150 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10';

    $labelClass = 'mb-1.5 block text-sm font-semibold text-gray-700';
@endphp

<div class="space-y-6">

    {{-- Part Information --}}
    <div>
        <div class="mb-4">
            <h3 class="text-base font-semibold text-gray-900">
                Part Information
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Enter the basic information about this spare part.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

            {{-- Part Name --}}
            <div>
                <label for="name" class="{{ $labelClass }}">
                    Part Name <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $part->name ?? '') }}"
                    placeholder="e.g. Engine Oil Filter"
                    required
                    maxlength="255"
                    class="{{ $inputClass }} @error('name') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                >

                @error('name')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Part Code --}}
            <div>
                <label for="part_code" class="{{ $labelClass }}">
                    Part Code
                </label>

                <input
                    type="text"
                    name="part_code"
                    id="part_code"
                    value="{{ old('part_code', $part->part_code ?? '') }}"
                    placeholder="e.g. FLT-001"
                    maxlength="100"
                    class="{{ $inputClass }} @error('part_code') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                >

                @error('part_code')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category --}}
            <div>
                <label for="category" class="{{ $labelClass }}">
                    Category
                </label>

                <input
                    type="text"
                    name="category"
                    id="category"
                    value="{{ old('category', $part->category ?? '') }}"
                    placeholder="e.g. Engine, Electrical, Hydraulic"
                    maxlength="100"
                    class="{{ $inputClass }} @error('category') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                >

                @error('category')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Unit --}}
            <div>
                <label for="unit" class="{{ $labelClass }}">
                    Unit <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    name="unit"
                    id="unit"
                    value="{{ old('unit', $part->unit ?? '') }}"
                    placeholder="e.g. Piece, Litre, Set"
                    required
                    maxlength="50"
                    class="{{ $inputClass }} @error('unit') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                >

                @error('unit')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- Stock and Pricing --}}
    <div class="border-t border-gray-100 pt-6">

        <div class="mb-4">
            <h3 class="text-base font-semibold text-gray-900">
                Stock and Pricing
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Set the default cost and available stock quantity.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

            {{-- Unit Cost --}}
            <div>
                <label for="default_unit_cost" class="{{ $labelClass }}">
                    Default Unit Cost <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm font-medium text-gray-500">
                        $
                    </span>

                    <input
                        type="number"
                        name="default_unit_cost"
                        id="default_unit_cost"
                        value="{{ old('default_unit_cost', $part->default_unit_cost ?? '0.00') }}"
                        min="0"
                        step="0.01"
                        required
                        placeholder="0.00"
                        class="{{ $inputClass }} pl-8 @error('default_unit_cost') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                    >
                </div>

                <p class="mt-1.5 text-xs text-gray-500">
                    Default cost per unit.
                </p>

                @error('default_unit_cost')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Stock Quantity --}}
            <div>
                <label for="stock_quantity" class="{{ $labelClass }}">
                    Stock Quantity <span class="text-red-500">*</span>
                </label>

                <input
                    type="number"
                    name="stock_quantity"
                    id="stock_quantity"
                    value="{{ old('stock_quantity', $part->stock_quantity ?? 0) }}"
                    min="0"
                    step="1"
                    required
                    placeholder="Enter available quantity"
                    class="{{ $inputClass }} @error('stock_quantity') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                >

                <p class="mt-1.5 text-xs text-gray-500">
                    Current available stock.
                </p>

                @error('stock_quantity')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- Additional Information --}}
    <div class="border-t border-gray-100 pt-6">

        <div class="mb-4">
            <h3 class="text-base font-semibold text-gray-900">
                Additional Information
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Add optional notes about this part.
            </p>
        </div>

        <div>
            <label for="notes" class="{{ $labelClass }}">
                Notes
            </label>

            <textarea
                name="notes"
                id="notes"
                rows="4"
                placeholder="Enter additional details, specifications, or remarks..."
                class="{{ $inputClass }} resize-y @error('notes') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
            >{{ old('notes', $part->notes ?? '') }}</textarea>

            @error('notes')
                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </div>

    {{-- Actions --}}
    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">

        <a
            href="{{ $cancelUrl ?? route('parts.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-200"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20"
        >
            {{ $isEdit ? 'Update' : 'Save' }}
        </button>

    </div>

</div>
