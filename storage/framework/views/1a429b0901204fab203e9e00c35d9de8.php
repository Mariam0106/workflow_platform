<?php
    $isDisabled = $disabled ?? false;
    $isActive = ! $isDisabled && request()->routeIs($routePattern ?? $route);
?>

<li>
    <?php if($isDisabled): ?>
        <span title="<?php echo e($label); ?>" class="flex items-center gap-2.5 rounded-lg border-l-2 border-transparent py-[7px] pl-2 pr-2.5 text-[13px] text-slate-300">
            <?php echo $__env->make('layouts.partials.icon', ['name' => $icon ?? '', 'class' => 'h-[18px] w-[18px] shrink-0'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <span class="sidebar-label flex-1"><?php echo e($label); ?></span>
            <span class="sidebar-label rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-400">Bientôt</span>
        </span>
    <?php else: ?>
        <a href="<?php echo e(route($route)); ?>" title="<?php echo e($label); ?>"
           class="flex items-center gap-2.5 rounded-lg border-l-2 py-[7px] pl-2 pr-2.5 text-[13px] font-medium transition
                  <?php echo e($isActive ? 'border-brand-blue bg-brand-blue/[0.08] text-brand-blue' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-brand-navy'); ?>">
            <?php echo $__env->make('layouts.partials.icon', ['name' => $icon ?? '', 'class' => 'h-[18px] w-[18px] shrink-0 ' . ($isActive ? 'text-brand-blue' : 'text-slate-400')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <span class="sidebar-label"><?php echo e($label); ?></span>
        </a>
    <?php endif; ?>
</li>
<?php /**PATH C:\projects\to step 12 backup\resources\views/layouts/partials/nav-item.blade.php ENDPATH**/ ?>