
<div class="overflow-x-auto">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-6 py-3">#</th>
                <th class="px-6 py-3">Defect Type</th>
                <th class="px-6 py-3">Description</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse($defectTypes as $defectType)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-500">
                        {{ $defectTypes->firstItem() + $loop->index }}
                    </td>

                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $defectType->name }}
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        <span class="block max-w-md truncate">
                            {{ $defectType->description ?: '-' }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <span @class([
                            'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                            'bg-green-100 text-green-700' => $defectType->is_active,
                            'bg-gray-100 text-gray-600' => !$defectType->is_active,
                        ])>
                            {{ $defectType->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('defect-types.edit', $defectType) }}"
                                class="rounded-lg border px-3 py-1.5 text-gray-700 transition
                                       hover:border-gray-400 hover:bg-gray-100">
                                Edit
                            </a>

                            <form method="POST"
                                action="{{ route('defect-types.toggle-active', $defectType) }}"
                                onsubmit="return confirm('Are you sure you want to {{ $defectType->is_active ? 'deactivate' : 'activate' }} this defect type?');">

                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="rounded-lg border px-3 py-1.5 transition
                                    {{ $defectType->is_active
                                        ? 'border-red-200 text-red-600 hover:bg-red-50'
                                        : 'border-green-200 text-green-700 hover:bg-green-50' }}">
                                    {{ $defectType->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="font-medium">No defect types found.</div>
                        <p class="mt-1 text-xs">
                            Try changing your search or filter.
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="border-t border-gray-100 px-4 py-3">
    {{ $defectTypes->links('vendors.pagination.custom') }}
</div>
