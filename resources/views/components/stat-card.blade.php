@props([
    'title',
    'value',
    'icon',
])

<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
    <div class="flex items-center justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm text-gray-500">
                {{ $title }}
            </p>

            <p class="mt-2 text-2xl font-bold text-gray-900">
                {{ $value }}
            </p>
        </div>

        {{-- Icon container --}}
        <div @class([
            'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
            'bg-blue-50 text-blue-700' => $icon === 'clipboard',
            'bg-emerald-50 text-emerald-700' => $icon === 'banknote',
            'bg-green-50 text-green-700' => $icon === 'check-circle',
            'bg-amber-50 text-amber-700' => $icon === 'wrench',
            'bg-indigo-50 text-indigo-700' => $icon === 'truck',
            'bg-gray-100 text-gray-600' => !in_array($icon, [
                'clipboard',
                'banknote',
                'check-circle',
                'wrench',
                'truck',
            ]),
        ])>

            @if ($icon === 'clipboard')
                {{-- Maintenance Records --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                    <rect x="9" y="3" width="6" height="4" rx="1" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6" />
                </svg>

            @elseif ($icon === 'banknote')
                {{-- Total Cost / Currency --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">

                    <circle cx="12" cy="12" r="9" />

                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 8.5h-4a2 2 0 100 4h2a2 2 0 110 4H9" />

                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.5v11" />
                </svg>

            @elseif ($icon === 'check-circle')
                {{-- Completed --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12l2.5 2.5L16 9" />
                </svg>

            @elseif ($icon === 'wrench')
                {{-- In Progress --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.7 6.3a5 5 0 00-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 006.4-6.4l-3 3-3-3 3-3z" />
                </svg>

            @elseif ($icon === 'truck')
                {{-- Vehicles in Maintenance --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h11v11H3zM14 11h4l3 4v3h-7z" />
                    <circle cx="7" cy="18" r="2" />
                    <circle cx="18" cy="18" r="2" />
                </svg>
            @endif

        </div>
    </div>
</div>