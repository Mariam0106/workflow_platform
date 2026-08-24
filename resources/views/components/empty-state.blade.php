@props(['icon' => 'file', 'title', 'description' => null])

<div class="flex flex-col items-center justify-center px-6 py-16 text-center">
    <span class="mb-3.5 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-5 w-5'])
    </span>
    <p class="text-sm font-medium text-brand-navy">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-[13px] text-slate-500">{{ $description }}</p>
    @endif
    @isset($actions)
        <div class="mt-4">{{ $actions }}</div>
    @endisset
</div>
