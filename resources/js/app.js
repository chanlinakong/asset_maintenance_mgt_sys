import Alpine from 'alpinejs';

import * as pdfjsLib from 'pdfjs-dist';
import pdfWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorker;

window.renderPdfThumbnail = async function (url) {
    if (!url || typeof url !== 'string') {
        throw new Error('Invalid PDF URL: ' + url);
    }

    const pdf = await pdfjsLib.getDocument({
        url: url,
    }).promise;

    const page = await pdf.getPage(1);

    const viewport = page.getViewport({ scale: 0.5 });

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    canvas.width = Math.ceil(viewport.width);
    canvas.height = Math.ceil(viewport.height);

    await page.render({
        canvasContext: context,
        viewport: viewport,
    }).promise;

    return canvas.toDataURL('image/png');
};

window.vehicleAttachmentPreviews = function (existingFiles) {
    return {
        files: existingFiles || [],
        visibleCount: 5,

        get visibleFiles() {
            return this.files.slice(0, this.visibleCount);
        },

        get hasMore() {
            return this.visibleCount < this.files.length;
        },

        loadMore() {
            this.visibleCount = Math.min(
                this.visibleCount + 5,
                this.files.length
            );
        },

        async initialize() {
            for (const file of this.files) {
                await this.loadPreview(file.id);
            }
        },

        async loadPreview(fileId) {
            const index = this.files.findIndex(
                file => file.id === fileId
            );

            if (index === -1) return;

            const file = this.files[index];
            const imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (imageExtensions.includes(file.extension)) {
                this.files[index] = {
                    ...file,
                    preview: file.url,
                };

                return;
            }

            if (file.extension === 'pdf') {
                try {
                    const preview = await window.renderPdfThumbnail(file.url);

                    const currentIndex = this.files.findIndex(
                        item => item.id === fileId
                    );

                    if (currentIndex !== -1) {
                        this.files[currentIndex] = {
                            ...this.files[currentIndex],
                            preview,
                        };
                    }
                } catch (error) {
                    console.error(
                        `Could not generate PDF preview for ${file.name}:`,
                        error
                    );
                }
            }
        },
    };
};

window.Alpine = Alpine;

Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                console.log(
                    'Service Worker registered:',
                    registration.scope
                );
            })
            .catch((error) => {
                console.error(
                    'Service Worker registration failed:',
                    error
                );
            });
    });
}