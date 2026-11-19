@if ($vehicle->exists)

    <div x-data="vehicleUploads(
                                                                    {{ $vehicle->id }},
                                                                    @js($vehicle->attachments->map(fn($file) => [
                                                                        'id' => $file->id,
                                                                        'name' => $file->original_name,
                                                                        'size' => $file->file_size,
                                                                        'extension' => strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)),
                                                                        'url' => asset('storage/' . $file->file_path),
                                                                        'status' => 'complete',
                                                                        'progress' => 100,
                                                                        'isExisting' => true,
                                                                        'deleting' => false,
                                                                    ])->values()),
                                                                    @js(route('vehicles.attachments.store', $vehicle))
                                                                )" x-init="initializePreviews()"
        @open-vehicle-uploads.window="openModal($event)" @keydown.escape.window="closeModal()">

        <div x-show="open" x-cloak x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center bg-blue-100/70 p-3 backdrop-blur-sm sm:p-6"
            aria-modal="true" role="dialog" aria-labelledby="upload-modal-title" @click.self="closeModal()"
            @keydown.tab.prevent="trapFocus($event)">

            <div x-ref="modal" @click.stop
                class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-7">

                    <div>
                        <h2 id="upload-modal-title" class="text-xl font-semibold text-gray-800 sm:text-2xl">
                            Upload files
                        </h2>

                        <p class="mt-1 text-xs text-gray-500">
                            Upload documents and pictures for this vehicle.
                        </p>
                    </div>

                    <button type="button" @click="closeModal()" aria-label="Close modal" title="Close" class="group inline-flex h-9 w-9 items-center justify-center
                   rounded-full text-gray-400
                   transition-all duration-200
                   hover:bg-red-50 hover:text-red-600
                   focus:outline-none focus:ring-0
                   active:bg-red-100">
                        <svg class="h-5 w-5 transition-transform duration-200 group-hover:rotate-90" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>

                </div>

                {{-- Scrollable body --}}
                <div class="overflow-y-auto p-5 sm:p-7">

                    {{-- Drop zone --}}
                    <div @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                        @drop.prevent="handleDrop($event)" @click="$refs.fileInput.click()"
                        @keydown.enter.prevent="$refs.fileInput.click()" @keydown.space.prevent="$refs.fileInput.click()"
                        tabindex="0" role="button" aria-label="Choose files to upload"
                        :class="dragging
                                                                                    ? 'border-blue-500 bg-blue-50'
                                                                                    : 'border-gray-300 bg-gray-50 hover:border-blue-400'"
                        class="cursor-pointer rounded-xl border-2 border-dashed px-4 py-9 text-center transition focus:outline-none focus:ring-2 focus:ring-blue-500 sm:py-12">

                        <input x-ref="fileInput" type="file" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                            class="hidden" @change="handleFiles($event.target.files); $event.target.value = ''">

                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center
                rounded-full border-2 border-dashed border-blue-300 bg-blue-50">
                            <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                    d="M12 16V4m0 0L7 9m5-5l5 5M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3" />
                            </svg>
                        </div>

                        <p class="text-sm text-gray-700 sm:text-base">
                            Drag and drop files here or
                            <span class="font-semibold text-blue-600 underline">
                                browse
                            </span>
                        </p>

                        <p class="mt-2 text-xs text-gray-500">
                            Allowed: PDF, DOC, DOCX, JPG, JPEG, PNG, WEBP
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            Images: max 5 MB. Documents: max 10 MB.
                        </p>

                    </div>

                    {{-- Errors --}}
                    <div class="mt-3 space-y-2" aria-live="polite">

                        <template x-for="error in errors" :key="error.id">
                            <div class="flex items-start justify-between gap-3 rounded-lg bg-red-50 px-3 py-2">

                                <p class="text-sm text-red-600" x-text="error.message"></p>

                                <button type="button" @click="dismissError(error.id)" aria-label="Dismiss error"
                                    class="shrink-0 text-lg leading-none text-red-400 hover:text-red-700">
                                    &times;
                                </button>

                            </div>
                        </template>

                    </div>

                    {{-- File list --}}
                    <div class="mt-5 space-y-3">

                        <template x-for="file in files" :key="file.id">

                            <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm">

                                {{-- File type icon --}}
                                <!-- <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-xs font-bold"
                                                                        :class="file.extension === 'pdf'
                                                                                                ? 'bg-red-50 text-red-600'
                                                                                                : ['doc', 'docx'].includes(file.extension)
                                                                                                    ? 'bg-blue-50 text-blue-600'
                                                                                                    : ['jpg', 'jpeg', 'png', 'webp'].includes(file.extension)
                                                                                                        ? 'bg-green-50 text-green-600'
                                                                                                        : 'bg-gray-100 text-gray-600'"
                                                                        x-text="file.extension.toUpperCase()">
                                                                    </div> -->

                                {{-- File preview --}}
                                <div
                                    class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">

                                    {{-- Actual image or PDF thumbnail --}}
                                    <template x-if="file.preview">
                                        <img :src="file.preview" :alt="file.name" class="h-full w-full object-cover">
                                    </template>

                                    {{-- Fallback for documents or files without previews --}}
                                    <template x-if="!file.preview">
                                        <div class="flex h-full w-full flex-col items-center justify-center gap-1" :class="file.extension === 'pdf'
                                                    ? 'bg-red-50 text-red-600'
                                                    : ['doc', 'docx'].includes(file.extension)
                                                        ? 'bg-blue-50 text-blue-600'
                                                        : 'bg-gray-100 text-gray-500'">
                                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M14 3v5h5M9 13h6M9 17h6" />
                                            </svg>

                                            <span class="text-[9px] font-bold uppercase" x-text="file.extension"></span>
                                        </div>
                                    </template>

                                </div>

                                {{-- File information --}}
                                <div class="min-w-0 flex-1">

                                    <template x-if="file.status === 'complete'">
                                        <a :href="file.url" target="_blank" rel="noopener noreferrer" @click.stop
                                            class="block truncate text-sm font-medium text-gray-700 hover:text-blue-600"
                                            x-text="file.name"></a>
                                    </template>

                                    <template x-if="file.status !== 'complete'">
                                        <p class="truncate text-sm font-medium text-gray-700" x-text="file.name"></p>
                                    </template>

                                    {{-- Uploading --}}
                                    <template x-if="file.status === 'uploading'">
                                        <div class="mt-2">
                                            <div class="h-1.5 overflow-hidden rounded-full bg-gray-200">
                                                <div class="h-full rounded-full bg-blue-500 transition-all duration-150"
                                                    :style="`width: ${file.progress}%`"></div>
                                            </div>

                                            <p class="mt-1 text-xs text-gray-400" x-text="`${file.progress}% uploaded`"></p>
                                        </div>
                                    </template>

                                    {{-- Complete --}}
                                    <template x-if="file.status === 'complete'">
                                        <div class="mt-1 flex items-center gap-2">
                                            <p class="text-xs text-gray-400" x-text="formatSize(file.size)"></p>
                                            <span class="text-xs text-green-600">Uploaded</span>
                                        </div>
                                    </template>

                                </div>

                                {{-- Remove --}}
                                <button type="button" @click.stop="removeFile(file)" :disabled="file.deleting"
                                    :aria-label="'Delete ' + file.name" title="Delete attachment" class="group shrink-0 rounded-xl p-2.5 text-gray-400
                           transition-all duration-200
                           hover:bg-red-50 hover:text-red-600
                           focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1
                           disabled:cursor-wait disabled:opacity-50">
                                    {{-- Loading spinner --}}
                                    <template x-if="file.deleting">
                                        <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor"
                                                stroke-width="3" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M21 12a9 9 0 0 0-9-9v3a6 6 0 0 1 6 6h3Z" />
                                        </svg>
                                    </template>

                                    {{-- Modern trash icon --}}
                                    <template x-if="!file.deleting">
                                        <svg class="h-5 w-5 transition-transform duration-200 group-hover:scale-110"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M8 6V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V6" />
                                            <path d="m19 6-.9 13.1a2 2 0 0 1-2 1.9H7.9a2 2 0 0 1-2-1.9L5 6" />
                                            <path d="M10 11v6" />
                                            <path d="M14 11v6" />
                                        </svg>
                                    </template>
                                </button>

                            </div>

                        </template>

                    </div>

                    <p x-show="files.length === 0" class="py-6 text-center text-sm text-gray-400">
                        No attachments uploaded yet.
                    </p>

                </div>

            </div>
        </div>

    </div>

@endif

<script>
    window.vehicleUploads = function (vehicleId, existingFiles, uploadUrl) {
        return {
            vehicleId,

            open: false,
            dragging: false,

            files: existingFiles || [],
            errors: [],
            requests: {},

            uploadUrl,

            csrfToken: document.querySelector(
                'meta[name="csrf-token"]'
            )?.content,

            opener: null,

            allowedExtensions: [
                'pdf', 'doc', 'docx',
                'jpg', 'jpeg', 'png', 'webp'
            ],

            openModal(event) {
                this.opener = event?.target || document.activeElement;
                this.open = true;

                this.$nextTick(() => {
                    this.$refs.closeButton?.focus();
                });
            },

            closeModal() {
                // Do not close while uploads are still running.
                const hasUploadingFiles = this.files.some(
                    file => file.status === 'uploading'
                );

                if (hasUploadingFiles) {
                    this.showError(
                        'Please wait until the uploads finish before closing.'
                    );

                    return;
                }

                this.open = false;

                this.$nextTick(() => {
                    this.opener?.focus();
                });
            },

            trapFocus(event) {
                const elements = [
                    ...this.$refs.modal.querySelectorAll(
                        'button:not(:disabled), a[href], input:not(:disabled), [tabindex="0"]'
                    )
                ].filter(element => element.offsetParent !== null);

                if (!elements.length) return;

                const first = elements[0];
                const last = elements[elements.length - 1];

                if (event.shiftKey && document.activeElement === first) {
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    first.focus();
                }
            },

            handleDrop(event) {
                this.dragging = false;
                this.handleFiles(event.dataTransfer.files);
            },

            handleFiles(selectedFiles) {
                const selected = Array.from(selectedFiles);

                if (selected.length > 10) {
                    this.showError(
                        'You can select a maximum of 10 files at a time.'
                    );

                    return;
                }

                selected.forEach(file => {
                    const extension = file.name
                        .split('.')
                        .pop()
                        .toLowerCase();

                    if (!this.allowedExtensions.includes(extension)) {
                        this.showError(
                            `${file.name}: unsupported file type.`
                        );

                        return;
                    }

                    const isImage = [
                        'jpg', 'jpeg', 'png', 'webp'
                    ].includes(extension);

                    const maxSize = isImage
                        ? 5 * 1024 * 1024
                        : 10 * 1024 * 1024;

                    const maxSizeLabel = isImage ? '5 MB' : '10 MB';

                    if (file.size === 0) {
                        this.showError(
                            `${file.name}: empty files cannot be uploaded.`
                        );

                        return;
                    }

                    if (file.size > maxSize) {
                        this.showError(
                            `${file.name}: maximum allowed size is ${maxSizeLabel}.`
                        );

                        return;
                    }

                    const temporaryId = crypto.randomUUID();

                    const item = {
                        id: temporaryId,
                        name: file.name,
                        size: file.size,
                        extension,
                        progress: 0,
                        status: 'uploading',
                        url: null,
                        preview: null,
                        isExisting: false,
                        deleting: false,
                    };

                    this.files.push(item);

                    this.uploadFile(file, item, temporaryId);
                });
            },

            initializePreviews() {
                this.files.forEach(file => {
                    this.loadPreview(file.id);
                });
            },

            async loadPreview(fileId) {
                const file = this.files.find(
                    item => item.id === fileId
                );

                if (!file || !file.url) return;

                const imageExtensions = [
                    'jpg', 'jpeg', 'png', 'webp'
                ];

                // Actual image thumbnail.
                if (imageExtensions.includes(file.extension)) {
                    file.preview = file.url;
                    return;
                }

                // First-page PDF thumbnail.
                if (file.extension === 'pdf') {
                    try {
                        if (typeof window.renderPdfThumbnail !== 'function') {
                            return;
                        }

                        file.preview = await window.renderPdfThumbnail(file.url);

                    } catch (error) {
                        console.error(
                            `Could not generate PDF preview for ${file.name}:`,
                            error
                        );
                    }
                }
            },

            uploadFile(file, item, temporaryId) {
                const xhr = new XMLHttpRequest();

                this.requests[temporaryId] = xhr;

                const formData = new FormData();
                formData.append('files[]', file);

                xhr.open('POST', this.uploadUrl);

                xhr.setRequestHeader('X-CSRF-TOKEN', this.csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');

                // Update the reactive Alpine files array.
                const updateFile = (changes) => {
                    const index = this.files.findIndex(
                        current => current.id === temporaryId
                    );

                    if (index !== -1) {
                        this.files[index] = {
                            ...this.files[index],
                            ...changes,
                        };
                    }
                };

                // Upload progress.
                xhr.upload.addEventListener('progress', (event) => {
                    if (!event.lengthComputable) return;

                    const progress = Math.round(
                        (event.loaded / event.total) * 100
                    );

                    updateFile({ progress });
                });

                // All request data has been sent.
                xhr.upload.addEventListener('load', () => {
                    updateFile({ progress: 100 });
                });

                // Server response received.
                xhr.addEventListener('load', () => {
                    delete this.requests[temporaryId];

                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            const attachment = response.attachments?.[0];

                            if (!attachment) {
                                throw new Error('Attachment response is missing.');
                            }

                            const index = this.files.findIndex(
                                current => current.id === temporaryId
                            );

                            if (index !== -1) {
                                this.files[index] = {
                                    ...this.files[index],
                                    id: attachment.id,
                                    name: attachment.name,
                                    size: attachment.size,
                                    extension: attachment.extension,
                                    url: attachment.url,
                                    progress: 100,
                                    status: 'complete',
                                    isExisting: true,
                                };
                                // Generate the thumbnail after successful upload.
                                this.loadPreview(attachment.id);
                            }

                        } catch (error) {
                            this.files = this.files.filter(
                                current => current.id !== temporaryId
                            );

                            this.showError(
                                `Could not process the upload response for ${file.name}.`
                            );
                        }

                    } else {
                        this.files = this.files.filter(
                            current => current.id !== temporaryId
                        );

                        let message = `Failed to upload ${file.name}.`;

                        try {
                            const response = JSON.parse(xhr.responseText);

                            const validationErrors = Object.values(
                                response.errors || {}
                            ).flat();

                            if (validationErrors.length) {
                                message = validationErrors.join(' ');
                            } else if (response.message) {
                                message = response.message;
                            }
                        } catch (error) {
                            // Keep the default error message.
                        }

                        this.showError(message);
                    }
                });

                // Network error.
                xhr.addEventListener('error', () => {
                    delete this.requests[temporaryId];

                    this.files = this.files.filter(
                        current => current.id !== temporaryId
                    );

                    this.showError(
                        `Network error while uploading ${file.name}.`
                    );
                });

                // Request timeout.
                xhr.addEventListener('timeout', () => {
                    delete this.requests[temporaryId];

                    this.files = this.files.filter(
                        current => current.id !== temporaryId
                    );

                    this.showError(
                        `Upload timed out: ${file.name}.`
                    );
                });

                // Request cancelled.
                xhr.addEventListener('abort', () => {
                    delete this.requests[temporaryId];
                });

                xhr.send(formData);
            },

            async removeFile(file) {
                // Cancel an upload that is still in progress.
                if (file.status === 'uploading') {
                    this.requests[file.id]?.abort();

                    this.files = this.files.filter(
                        item => item !== file
                    );

                    return;
                }

                if (file.status !== 'complete') {
                    this.files = this.files.filter(
                        item => item !== file
                    );

                    return;
                }

                if (file.deleting) return;

                file.deleting = true;

                const controller = new AbortController();

                const timeoutId = setTimeout(() => {
                    controller.abort();
                }, 15000);

                try {
                    const response = await fetch(
                        `${this.uploadUrl}/${file.id}`,
                        {
                            method: 'DELETE',
                            signal: controller.signal,
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                            },
                        }
                    );

                    if (!response.ok) {
                        const result = await response.json().catch(() => ({}));

                        throw new Error(
                            result.message || `Delete failed (${response.status}).`
                        );
                    }

                    // Remove from the UI only after server confirmation.
                    this.files = this.files.filter(
                        item => item.id !== file.id
                    );

                } catch (error) {
                    console.error('Attachment deletion error:', error);

                    this.showError(
                        error.name === 'AbortError'
                            ? `Deleting ${file.name} timed out. Please try again.`
                            : error.message || `Could not remove ${file.name}.`
                    );

                } finally {
                    clearTimeout(timeoutId);

                    // Reset loading state if the file remains in the list.
                    const currentFile = this.files.find(
                        item => item.id === file.id
                    );

                    if (currentFile) {
                        currentFile.deleting = false;
                    }
                }
            },

            formatSize(bytes) {
                if (bytes < 1024) {
                    return `${bytes} B`;
                }

                if (bytes < 1024 * 1024) {
                    return `${(bytes / 1024).toFixed(1)} KB`;
                }

                return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
            },

            showError(message) {
                const id = crypto.randomUUID();

                this.errors.push({
                    id,
                    message,
                });

                setTimeout(() => {
                    this.dismissError(id);
                }, 6000);
            },

            dismissError(id) {
                this.errors = this.errors.filter(
                    error => error.id !== id
                );
            },
        };
    };
</script>