@props([
    'title',
    'value',
    'icon',
])

<div class="rounded-xl bg-white p-5 shadow-sm">

    <div class="flex items-center justify-between">

        <div>

            <p class="text-sm text-gray-500">
                {{ $title }}
            </p>

            <p class="mt-2 text-3xl font-bold">
                {{ $value }}
            </p>

        </div>

        <div
            class="flex h-12 w-12 items-center
                   justify-center rounded-xl
                   bg-gray-100 text-xl"
        >
            {{ $icon }}
        </div>

    </div>

</div>