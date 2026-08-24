@props(['name', 'label' => null, 'type' => 'text', 'required' => false, 'hint' => null, 'value' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-[13px] font-medium text-slate-700">
            {{ $label }} @if ($required) <span class="text-brand-danger">*</span> @endif
        </label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $type === 'password' ? '' : old($name, $value ?? '') }}"
        @if ($required) required @endif
        @if ($name === 'code')
            {{-- Bloque l'espace directement à la frappe (pas seulement
                 après coup côté serveur) - "code" est un identifiant
                 technique (workflows, formulaires, entités,
                 départements, étapes...), jamais destiné à contenir un
                 espace (déjà rejeté par la validation "alpha_dash",
                 ceci l'empêche simplement plus tôt, à la saisie). --}}
            onkeydown="if (event.key === ' ') event.preventDefault();"
            oninput="this.value = this.value.replace(/\s/g, '')"
            onpaste="setTimeout(() => { this.value = this.value.replace(/\s/g, ''); })"
        @endif
        {{ $attributes->class([
            'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-4',
            'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has($name),
            'border-brand-border focus:border-brand-blue focus:ring-brand-blue/10' => ! $errors->has($name),
        ]) }}
    >

    @if ($hint && ! $errors->has($name))
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p>
    @enderror
</div>