<table class="min-w-full text-left text-sm">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">
                                Code
                            </th>

                            <th class="px-6 py-3">
                                Name
                            </th>

                            <th class="px-6 py-3">
                                Type
                            </th>

                            <th class="px-6 py-3">
                                Registration
                            </th>

                            <th class="px-6 py-3">
                                Status
                            </th>

                            <th class="px-6 py-3 text-right">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @forelse($vehicles as $vehicle)

                            <tr>

                                <td class="px-6 py-4 font-medium">
                                    {{ $vehicle->vehicle_code }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $vehicle->name }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $vehicle->type }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $vehicle->registration_number ?? '-' }}
                                </td>

                                @php
                                    $status = $vehicle->status;
                                @endphp


                                <td class="px-6 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                        'bg-green-100 text-green-700' =>
                                            $status->value === 'active',
                                        'bg-yellow-100 text-yellow-700' =>
                                            $status->value === 'maintenance',
                                        'bg-red-100 text-red-700' =>
                                            $status->value === 'out_of_service',
                                    ])>
                                        {{ str($status->value)->replace('_', ' ')->title() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('vehicles.show', $vehicle) }}" class="rounded-lg border px-3 py-1.5 transition-colors
                                                            hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900
                                                            focus:outline-none focus:ring-2 focus:ring-gray-300">
                                            View
                                        </a>

                                        @if(auth()->user()->role->value === 'admin')
                                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="rounded-lg border px-3 py-1.5 transition-colors
                                                                    hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900
                                                                    focus:outline-none focus:ring-2 focus:ring-gray-300">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
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
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    No vehicles found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

                 {{ $vehicles->links('vendors.pagination.custom') }}