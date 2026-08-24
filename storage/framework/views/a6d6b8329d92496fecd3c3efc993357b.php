<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'label' => null, 'type' => 'text', 'required' => false, 'hint' => null, 'value' => null]));

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

foreach (array_filter((['name', 'label' => null, 'type' => 'text', 'required' => false, 'hint' => null, 'value' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div>
    <?php if($label): ?>
        <label for="<?php echo e($name); ?>" class="mb-1.5 block text-[13px] font-medium text-slate-700">
            <?php echo e($label); ?> <?php if($required): ?> <span class="text-brand-danger">*</span> <?php endif; ?>
        </label>
    <?php endif; ?>

    <input
        id="<?php echo e($name); ?>"
        name="<?php echo e($name); ?>"
        type="<?php echo e($type); ?>"
        value="<?php echo e($type === 'password' ? '' : old($name, $value ?? '')); ?>"
        <?php if($required): ?> required <?php endif; ?>
        <?php if($name === 'code'): ?>
            
            onkeydown="if (event.key === ' ') event.preventDefault();"
            oninput="this.value = this.value.replace(/\s/g, '')"
            onpaste="setTimeout(() => { this.value = this.value.replace(/\s/g, ''); })"
        <?php endif; ?>
        <?php echo e($attributes->class([
            'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-4',
            'border-red-300 focus:border-red-400 focus:ring-red-100' => $errors->has($name),
            'border-brand-border focus:border-brand-blue focus:ring-brand-blue/10' => ! $errors->has($name),
        ])); ?>

    >

    <?php if($hint && ! $errors->has($name)): ?>
        <p class="mt-1 text-xs text-slate-400"><?php echo e($hint); ?></p>
    <?php endif; ?>

    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1 text-xs text-brand-danger"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div><?php /**PATH C:\projects\to step 12 backup\resources\views/components/form-input.blade.php ENDPATH**/ ?>