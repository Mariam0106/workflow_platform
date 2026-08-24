@props(['value' => null, 'placeholder' => 'Rechercher…'])

<form method="GET" class="relative w-full max-w-xs">
    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
        @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
    </span>
    <input
        type="text"
        name="q"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        {{ $attributes->class([
            'h-9 w-full rounded-lg border border-brand-border bg-white pl-9 pr-3 text-[13px] text-brand-navy shadow-sm transition placeholder:text-slate-400 hover:border-brand-blue/40 focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10',
        ]) }}
    >
</form>
