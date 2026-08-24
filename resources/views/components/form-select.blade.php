@props(['name', 'label' => null, 'options' => [], 'required' => false, 'placeholder' => 'Sélectionner —', 'valueKey' => 'id', 'labelKey' => 'name', 'hint' => null, 'value' => null])

@php
    $selected = old($name, $value ?? '');
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-[13px] font-medium text-slate-700">
            {{ $label }} @if ($required) <span class="text-brand-danger">*</span> @endif
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->class([
            'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:outline-none focus:ring-4',
            'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has($name),
            'border-brand-border focus:border-brand-blue focus:ring-brand-blue/10' => ! $errors->has($name),
        ]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $option)
            @php
                $optionValue = is_array($option) ? $option[$valueKey] : $option->{$valueKey};
                $optionLabel = is_array($option) ? $option[$labelKey] : $option->{$labelKey};
            @endphp
            <option value="{{ $optionValue }}" @selected((string) $selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p>
    @enderror
    @if ($hint && ! $errors->has($name))
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>