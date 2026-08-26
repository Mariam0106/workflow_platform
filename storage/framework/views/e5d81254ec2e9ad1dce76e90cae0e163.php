<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['value' => null, 'placeholder' => 'Rechercher…']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['value' => null, 'placeholder' => 'Rechercher…']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<form method="GET" class="relative w-full max-w-xs">
    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
        <?php echo $__env->make('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </span>
    <input
        type="text"
        name="q"
        value="<?php echo e($value); ?>"
        placeholder="<?php echo e($placeholder); ?>"
        autocomplete="off"
        <?php echo e($attributes->class([
            'h-9 w-full rounded-lg border border-brand-border bg-white pl-9 pr-3 text-[13px] text-brand-navy shadow-sm transition placeholder:text-slate-400 hover:border-brand-blue/40 focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10',
        ])); ?>

    >
</form>
<?php /**PATH C:\projects\to step 12 backup\resources\views/components/search-input.blade.php ENDPATH**/ ?>