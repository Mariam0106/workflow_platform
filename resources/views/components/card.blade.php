@props(['padded' => true])

<div {{ $attributes->class(['rounded-xl border border-brand-border bg-white', 'p-5' => $padded]) }}>
    {{ $slot }}
</div>
