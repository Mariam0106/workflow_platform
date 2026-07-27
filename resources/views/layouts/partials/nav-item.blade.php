@php
    $isDisabled = $disabled ?? false;
    $isActive = ! $isDisabled && request()->routeIs($routePattern ?? $route);
@endphp

<li>
    @if ($isDisabled)
        <span class="flex items-center gap-2.5 rounded-lg border-l-2 border-transparent py-[7px] pl-2 pr-2.5 text-[13px] text-slate-300">
            @include('layouts.partials.icon', ['name' => $icon ?? '', 'class' => 'h-[18px] w-[18px] shrink-0'])
            <span class="flex-1">{{ $label }}</span>
            <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-400">Bientôt</span>
        </span>
    @else
        <a href="{{ route($route) }}"
           class="flex items-center gap-2.5 rounded-lg border-l-2 py-[7px] pl-2 pr-2.5 text-[13px] font-medium transition
                  {{ $isActive ? 'border-brand-blue bg-brand-blue/[0.08] text-brand-blue' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-brand-navy' }}">
            @include('layouts.partials.icon', ['name' => $icon ?? '', 'class' => 'h-[18px] w-[18px] shrink-0 ' . ($isActive ? 'text-brand-blue' : 'text-slate-400')])
            <span>{{ $label }}</span>
        </a>
    @endif
</li>
