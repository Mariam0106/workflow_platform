@props(['active' => true, 'activeLabel' => 'Actif', 'inactiveLabel' => 'Inactif'])

<x-badge :tone="$active ? 'success' : 'slate'">
    <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-brand-success' : 'bg-slate-400' }}"></span>
    {{ $active ? $activeLabel : $inactiveLabel }}
</x-badge>
