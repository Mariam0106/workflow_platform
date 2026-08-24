@props(['paginator', 'onEachSide' => 1])

@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        // Fenêtre de pages numérotées autour de la page courante, plus
        // toujours la première et la dernière - avec des "…" pour les
        // trous. Nécessaire dès que l'historique dépasse une poignée de
        // pages : Précédent/Suivant seuls ne permettent pas de sauter
        // loin sans cliquer des dizaines de fois.
        $start = max($current - $onEachSide, 1);
        $end = min($current + $onEachSide, $last);

        $pages = range($start, $end);
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-brand-border px-5 py-3">
        <p class="text-[13px] text-slate-500">
            Page {{ $current }} sur {{ $last }}
            <span class="text-slate-300">·</span>
            {{ $paginator->total() }} entrée(s)
        </p>

        <nav class="flex items-center gap-1" aria-label="Pagination">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-300">
                    @include('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5'])
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" title="Page précédente"
                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">
                    @include('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5'])
                </a>
            @endif

            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}"
                   class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-[13px] font-medium text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">1</a>
                @if ($start > 2)
                    <span class="px-1 text-[13px] text-slate-400">…</span>
                @endif
            @endif

            @foreach ($pages as $page)
                @if ($page === $current)
                    <span aria-current="page"
                          class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-brand-blue px-2 text-[13px] font-semibold text-white shadow-sm shadow-brand-blue/30">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}"
                       class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-[13px] font-medium text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            @if ($end < $last)
                @if ($end < $last - 1)
                    <span class="px-1 text-[13px] text-slate-400">…</span>
                @endif
                <a href="{{ $paginator->url($last) }}"
                   class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-[13px] font-medium text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">{{ $last }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" title="Page suivante"
                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">
                    @include('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5 rotate-180'])
                </a>
            @else
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-300">
                    @include('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5 rotate-180'])
                </span>
            @endif
        </nav>
    </div>
@endif