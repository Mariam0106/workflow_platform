<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button', 'icon' => null]));

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

foreach (array_filter((['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button', 'icon' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition focus:outline-none focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50 active:scale-[0.98]';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-[13px]',
        'md' => 'px-4 py-2.5 text-sm',
    ];

    $variants = [
        'primary' => 'bg-brand-blue text-white shadow-sm shadow-brand-blue/30 hover:bg-brand-blue-dark hover:shadow-md hover:shadow-brand-blue/30 focus:ring-brand-blue/25',
        'secondary' => 'border border-brand-border bg-white text-brand-navy shadow-sm hover:border-brand-blue/30 hover:bg-brand-blue/[0.04] focus:ring-slate-200',
        'danger' => 'border border-red-200 bg-white text-brand-danger shadow-sm hover:bg-red-50 focus:ring-red-100',
        'ghost' => 'text-slate-500 hover:bg-slate-100 hover:text-brand-navy focus:ring-slate-200',
    ];

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
    $tag = $href ? 'a' : 'button';
?>

<<?php echo e($tag); ?>

    <?php if($href): ?> href="<?php echo e($href); ?>" <?php else: ?> type="<?php echo e($type); ?>" <?php endif; ?>
    <?php echo e($attributes->class($classes)); ?>

>
    <?php if($icon): ?>
        <?php echo $__env->make('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4 shrink-0'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    <?php echo e($slot); ?>

</<?php echo e($tag); ?>>
<?php /**PATH C:\projects\to step 12 backup\resources\views/components/button.blade.php ENDPATH**/ ?>