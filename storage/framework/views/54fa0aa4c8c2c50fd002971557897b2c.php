<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'value', 'icon' => 'file', 'accent' => 'blue', 'href' => null]));

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

foreach (array_filter((['label', 'value', 'icon' => 'file', 'accent' => 'blue', 'href' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $accents = [
        'blue' => 'bg-brand-blue/10 text-brand-blue',
        'success' => 'bg-green-50 text-brand-success',
        'warning' => 'bg-amber-50 text-brand-warning',
        'danger' => 'bg-red-50 text-brand-danger',
        'slate' => 'bg-slate-100 text-slate-500',
    ];
    $accentClass = $accents[$accent] ?? $accents['blue'];
    $tag = $href ? 'a' : 'div';
?>

<<?php echo e($tag); ?> <?php if($href): ?> href="<?php echo e($href); ?>" <?php endif; ?>
    class="flex items-center gap-3.5 rounded-xl border border-brand-border bg-white p-4 transition <?php echo e($href ? 'hover:border-brand-blue/40 hover:shadow-sm' : ''); ?>">
    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg <?php echo e($accentClass); ?>">
        <?php echo $__env->make('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </span>
    <div class="min-w-0">
        <p class="text-2xl font-semibold leading-tight text-brand-navy"><?php echo e($value); ?></p>
        <p class="truncate text-[13px] text-slate-500"><?php echo e($label); ?></p>
    </div>
</<?php echo e($tag); ?>>
<?php /**PATH C:\projects\to step 12 backup\resources\views/components/kpi-card.blade.php ENDPATH**/ ?>