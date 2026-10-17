@if ($paginator->hasPages())
    <div
        class="flex flex-col gap-3 border-t border-gray-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

        {{-- Results count --}}
        <p class="text-sm text-gray-500">
            Showing
            <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-medium text-gray-900">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-medium text-gray-900">{{ $paginator->total() }}</span>
            results
        </p>

        {{-- Pagination links --}}
        <nav class="flex items-center justify-center gap-1" aria-label="Pagination">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-300">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                    Previous
                </a>
            @endif


            {{-- Compact page numbers --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();

                $pages = collect([1, $last])
                    ->merge(range(
                        max(1, $current - 1),
                        min($last, $current + 1)
                    ))
                    ->unique()
                    ->sort()
                    ->values();
            @endphp

            @php $previous = null; @endphp

            @foreach ($pages as $page)
                {{-- Ellipsis when pages are skipped --}}
                @if ($previous !== null && $page > $previous + 1)
                    <span class="px-1.5 text-sm text-gray-400">
                        ...
                    </span>
                @endif

                @if ($page == $current)
                    <span aria-current="page"
                        class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-gray-900 px-3 text-sm font-semibold text-white">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}"
                        class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-gray-200 px-3 text-sm font-medium text-gray-600 transition hover:border-gray-300 hover:bg-gray-50">
                        {{ $page }}
                    </a>
                @endif

                @php $previous = $page; @endphp
            @endforeach


            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                    Next
                </a>
            @else
                <span class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-300">
                    Next
                </span>
            @endif

        </nav>
    </div>
@endif