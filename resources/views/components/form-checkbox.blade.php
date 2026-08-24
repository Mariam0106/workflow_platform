@props(['name', 'label', 'checked' => false, 'hint' => null])

@php
    $isChecked = old($name, $checked) ? true : false;
@endphp

<label class="flex cursor-pointer items-start gap-2.5 rounded-lg p-1 text-sm text-brand-navy transition hover:bg-slate-50">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" @checked($isChecked)
           class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-blue focus:ring-2 focus:ring-brand-blue/30 focus:ring-offset-0">
    <span>
        <span class="font-medium">{{ $label }}</span>
        @if ($hint)
            <span class="block text-xs font-normal text-slate-400">{{ $hint }}</span>
        @endif
    </span>
</label>
