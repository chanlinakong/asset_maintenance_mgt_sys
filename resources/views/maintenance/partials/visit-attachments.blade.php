
<div class="mt-6 border-t border-gray-100 pt-5">

    {{-- Header --}}
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-semibold text-gray-900">
                    Visit Attachments
                </h3>

                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                    {{ $visit->attachments->count() }}
                </span>
            </div>

            <p class="mt-1 text-xs text-gray-500">
                Photos, invoices, receipts, and documents for this visit.
            </p>
        </div>

        @if(
            $canUpdate &&
            !$isCompleted &&
            !$isCancelled &&
            $visit->status === 'in_progress'
        )
            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700">
                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                Upload available
            </span>
        @else
            <span class="text-xs text-gray-400">
                View only
            </span>
        @endif

    </div>


    {{-- Upload Form --}}
    @if(
        $canUpdate &&
        !$isCompleted &&
        !$isCancelled &&
        $visit->status === 'in_progress'
    )

        <form
            method="POST"
            action="{{ route('maintenance.visits.attachments.store', [
                'maintenance' => $maintenance->id,
                'visit' => $visit->id,
            ]) }}"
            enctype="multipart/form-data"
            class="mt-4 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4"
        >
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- File --}}
                <div class="md:col-span-2">

                    <label
                        for="attachment_file_{{ $visit->id }}"
                        class="mb-1.5 block text-xs font-semibold text-gray-700"
                    >
                        Choose File
                    </label>

                    <input
                        type="file"
                        name="file"
                        id="attachment_file_{{ $visit->id }}"
                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                        required
                        class="block w-full rounded-lg border border-gray-300 bg-white text-sm file:mr-3 file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-gray-700 hover:file:bg-gray-200"
                    >

                    <p class="mt-1.5 text-xs text-gray-500">
                        JPG, JPEG, PNG, WebP, or PDF. Maximum 10 MB.
                    </p>

                    @error('file')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Attachment Type --}}
                <div>

                    <label
                        for="attachment_type_{{ $visit->id }}"
                        class="mb-1.5 block text-xs font-semibold text-gray-700"
                    >
                        Attachment Type
                    </label>

                    <select
                        name="attachment_type"
                        id="attachment_type_{{ $visit->id }}"
                        required
                        class="w-full rounded-lg border-gray-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="before">
                            Before Repair
                        </option>

                        <option value="after">
                            After Repair
                        </option>

                        <option value="invoice">
                            Invoice / Receipt
                        </option>

                        <option value="report">
                            Maintenance Report
                        </option>

                        <option value="other" selected>
                            Other
                        </option>
                    </select>

                    @error('attachment_type')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

            <div class="mt-4 flex justify-end">

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 16V4m0 0L7 9m5-5 5 5M5 16v3h14v-3"
                        />
                    </svg>

                    Upload Attachment
                </button>

            </div>

        </form>

    @endif


    {{-- Attachment List --}}
    <div class="mt-4">

        @forelse($visit->attachments as $attachment)

            <div class="group flex flex-col gap-3 border-b border-gray-100 py-4 last:border-b-0 sm:flex-row sm:items-center sm:justify-between">

                {{-- File Information --}}
                <div class="flex min-w-0 items-center gap-3">

                    {{-- Preview --}}
                    @if(
                        $attachment->file_type &&
                        str_starts_with($attachment->file_type, 'image/')
                    )

                        <a
                            href="{{ asset('storage/' . $attachment->file_path) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="shrink-0"
                        >
                            <img
                                src="{{ asset('storage/' . $attachment->file_path) }}"
                                alt="{{ $attachment->file_name }}"
                                class="h-14 w-14 rounded-lg border border-gray-200 object-cover transition group-hover:opacity-80"
                            >
                        </a>

                    @else

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600">

                            <svg
                                class="h-7 w-7"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M14 3v5h5M8 14h8M8 17h6"
                                />
                            </svg>

                        </div>

                    @endif


                    {{-- Details --}}
                    <div class="min-w-0">

                        <a
                            href="{{ asset('storage/' . $attachment->file_path) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="block truncate text-sm font-semibold text-gray-800 hover:text-indigo-600 hover:underline"
                            title="{{ $attachment->file_name }}"
                        >
                            {{ $attachment->file_name }}
                        </a>

                        <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500">

                            <span class="rounded-full bg-gray-100 px-2 py-0.5">
                                {{ str($attachment->attachment_type ?? 'other')->replace('_', ' ')->title() }}
                            </span>

                            <span>
                                {{ number_format(((int) ($attachment->file_size ?? 0)) / 1024, 1) }} KB
                            </span>

                            <span>
                                {{ $attachment->created_at?->format('d M Y, h:i A') }}
                            </span>

                        </div>

                        @if($attachment->uploader)
                            <p class="mt-1 text-xs text-gray-400">
                                Uploaded by {{ $attachment->uploader->name }}
                            </p>
                        @endif

                    </div>

                </div>


                {{-- Actions --}}
                <div class="flex shrink-0 items-center gap-3 pl-[68px] sm:pl-0">

                    <a
                        href="{{ asset('storage/' . $attachment->file_path) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                    >
                        View
                    </a>

                    @if(
                        $canUpdate &&
                        !$isCompleted &&
                        !$isCancelled &&
                        $visit->status === 'in_progress'
                    )

                        <form
                            method="POST"
                            action="{{ route('maintenance.visits.attachments.destroy', [
                                'maintenance' => $maintenance->id,
                                'visit' => $visit->id,
                                'attachment' => $attachment->id,
                            ]) }}"
                            onsubmit="return confirm('Are you sure you want to delete this attachment?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="text-xs font-semibold text-red-600 hover:text-red-800"
                            >
                                Delete
                            </button>

                        </form>

                    @endif

                </div>

            </div>

        @empty

            <div class="rounded-lg border border-gray-100 bg-gray-50 px-4 py-8 text-center">

                <svg
                    class="mx-auto h-8 w-8 text-gray-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2z"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14 3v5h5M8 14h8"
                    />
                </svg>

                <p class="mt-2 text-sm font-medium text-gray-600">
                    No attachments for this visit
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    Uploaded photos and documents will appear here.
                </p>

            </div>

        @endforelse

    </div>

</div>
