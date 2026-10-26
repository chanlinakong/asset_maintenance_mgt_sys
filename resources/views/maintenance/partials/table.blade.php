<table class="min-w-full text-left text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3">
                                Vehicle
                            </th>

                            <th class="px-6 py-3">
                                Maintenance
                            </th>

                            <th class="px-6 py-3">
                                Type
                            </th>

                            <th class="px-6 py-3">
                                Status
                            </th>

                            <th class="px-6 py-3">
                                Reported
                            </th>

                            <th class="px-6 py-3">
                                Cost
                            </th>

                            <th class="px-6 py-3 text-right">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @forelse($maintenanceRecords as $maintenance)

                                            <tr>

                                                <td class="px-6 py-4 font-medium">
                                                    {{ $maintenance->vehicle->vehicle_code }}
                                                </td>

                                                <td class="px-6 py-4">
                                                    {{ $maintenance->title }}
                                                </td>
                                                @php
                                                    $maintenanceType = $maintenance->type;
                                                @endphp

                                                <td class="px-6 py-4">
                                                    <span
                                                        class="inline-flex rounded-full bg-gray-100 px-3 py-1
                                                                                                                                                                                                                                                                                                                                   text-xs font-semibold text-gray-700">
                                                        {{ str($maintenanceType->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                    </span>
                                                </td>

                                                @php
                                                    $maintenanceStatus = $maintenance->status;
                                                @endphp

                                                <td class="px-6 py-4">
                                                    <span @class([
                                                        'inline-flex rounded-full px-3 py-1 text-xs font-semibold',

                                                        'bg-yellow-100 text-yellow-700' =>
                                                            $maintenanceStatus->value === 'pending',

                                                        'bg-blue-100 text-blue-700' =>
                                                            $maintenanceStatus->value === 'in_progress',

                                                        'bg-green-100 text-green-700' =>
                                                            $maintenanceStatus->value === 'completed',

                                                        'bg-red-100 text-red-700' =>
                                                            $maintenanceStatus->value === 'cancelled',
                                                    ])>
                                                        {{ str($maintenanceStatus->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                    </span>
                                                </td>

                                                <td class="px-6 py-4">
                                                    {{ $maintenance->reported_at
                                ->format('d M Y H:i') }}
                                                </td>

                                                <td class="px-6 py-4">
                                                    ${{ number_format(
                                (float) $maintenance->cost,
                                2
                            ) }}
                                                </td>

                                                <td class="px-6 py-4">
                                                    <div class="flex justify-end gap-2">

                                                        <a href="{{ route('maintenance.show', $maintenance) }}"
                                                            class="rounded-lg border px-3 py-1.5 transition-colors
                                                                hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900
                                                                focus:outline-none focus:ring-2 focus:ring-gray-300">View
                                                        </a>

                                                        <a href="{{ route('maintenance.edit', $maintenance) }}"
                                                            class="rounded-lg border px-3 py-1.5 transition-colors
                                                                hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900
                                                                focus:outline-none focus:ring-2 focus:ring-gray-300">
                                                            Edit
                                                        </a>

                                                        @if(auth()->user()->role->value === 'admin')
                                                            <form method="POST" action="{{ route('maintenance.destroy', $maintenance) }}"
                                                                onsubmit="return confirm('Are you sure you want to delete this maintenance record?')">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit"
                                                                    class="rounded-lg border px-3 py-1.5 transition-colors text-red-600 hover:bg-red-50 hover:border-red-300">
                                                                    Delete
                                                                </button>

                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>

                                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    No maintenance records found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

                {{ $maintenanceRecords->links('vendors.pagination.custom') }}