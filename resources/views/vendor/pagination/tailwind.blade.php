@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-slate-500">
            Mostrando
            <span class="font-semibold text-slate-700">{{ $paginator->firstItem() }}</span>
            a
            <span class="font-semibold text-slate-700">{{ $paginator->lastItem() }}</span>
            de
            <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span>
            resultado(s)
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-10 items-center rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-400">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-10 items-center rounded-md border border-emerald-200 bg-white px-3 text-sm font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-50">
                    Anterior
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-10 min-w-10 items-center justify-center rounded-md bg-emerald-600 px-3 text-sm font-bold text-white shadow-sm shadow-emerald-600/20">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-10 items-center rounded-md bg-slate-900 px-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Proxima
                </a>
            @else
                <span class="inline-flex h-10 items-center rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-400">
                    Proxima
                </span>
            @endif
        </div>
    </nav>
@endif
