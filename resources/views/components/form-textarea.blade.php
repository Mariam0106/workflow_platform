@props(['name', 'label' => null, 'required' => false, 'rows' => 3, 'value' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-[13px] font-medium text-slate-700">
            {{ $label }} @if ($required) <span class="text-brand-danger">*</span> @endif
        </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        {{ $attributes->class([
            'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-4',
            'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has($name),
            'border-brand-border focus:border-brand-blue focus:ring-brand-blue/10' => ! $errors->has($name),
        ]) }}
    >{{ old($name, $value ?? '') }}</textarea>

    @error($name)
        <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p>
    @enderror
</div>