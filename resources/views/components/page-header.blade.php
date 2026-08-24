@props(['title', 'description' => null])

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-brand-navy">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 items-center gap-2.5">
            {{ $actions }}
        </div>
    @endisset
</div>
