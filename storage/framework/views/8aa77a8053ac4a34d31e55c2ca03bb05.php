<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['tone' => 'slate']));

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

foreach (array_filter((['tone' => 'slate']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $tones = [
        'slate' => 'bg-slate-100 text-slate-600',
        'blue' => 'bg-brand-blue/10 text-brand-blue',
        'success' => 'bg-green-50 text-brand-success',
        'warning' => 'bg-amber-50 text-brand-warning',
        'danger' => 'bg-red-50 text-brand-danger',
        'navy' => 'bg-brand-navy/5 text-brand-navy',
    ];
?>

<span <?php echo e($attributes->class(['inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium', $tones[$tone] ?? $tones['slate']])); ?>>
    <?php echo e($slot); ?>

</span>
<?php /**PATH C:\projects\to step 12 backup\resources\views/components/badge.blade.php ENDPATH**/ ?>