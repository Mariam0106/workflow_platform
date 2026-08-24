<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['paginator', 'onEachSide' => 1]));

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

foreach (array_filter((['paginator', 'onEachSide' => 1]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($paginator->hasPages()): ?>
    <?php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        // Fenêtre de pages numérotées autour de la page courante, plus
        // toujours la première et la dernière - avec des "…" pour les
        // trous. Nécessaire dès que l'historique dépasse une poignée de
        // pages : Précédent/Suivant seuls ne permettent pas de sauter
        // loin sans cliquer des dizaines de fois.
        $start = max($current - $onEachSide, 1);
        $end = min($current + $onEachSide, $last);

        $pages = range($start, $end);
    ?>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-brand-border px-5 py-3">
        <p class="text-[13px] text-slate-500">
            Page <?php echo e($current); ?> sur <?php echo e($last); ?>

            <span class="text-slate-300">·</span>
            <?php echo e($paginator->total()); ?> entrée(s)
        </p>

        <nav class="flex items-center gap-1" aria-label="Pagination">
            <?php if($paginator->onFirstPage()): ?>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-300">
                    <?php echo $__env->make('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </span>
            <?php else: ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>" title="Page précédente"
                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">
                    <?php echo $__env->make('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </a>
            <?php endif; ?>

            <?php if($start > 1): ?>
                <a href="<?php echo e($paginator->url(1)); ?>"
                   class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-[13px] font-medium text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">1</a>
                <?php if($start > 2): ?>
                    <span class="px-1 text-[13px] text-slate-400">…</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($page === $current): ?>
                    <span aria-current="page"
                          class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-brand-blue px-2 text-[13px] font-semibold text-white shadow-sm shadow-brand-blue/30">
                        <?php echo e($page); ?>

                    </span>
                <?php else: ?>
                    <a href="<?php echo e($paginator->url($page)); ?>"
                       class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-[13px] font-medium text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">
                        <?php echo e($page); ?>

                    </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($end < $last): ?>
                <?php if($end < $last - 1): ?>
                    <span class="px-1 text-[13px] text-slate-400">…</span>
                <?php endif; ?>
                <a href="<?php echo e($paginator->url($last)); ?>"
                   class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-[13px] font-medium text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy"><?php echo e($last); ?></a>
            <?php endif; ?>

            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>" title="Page suivante"
                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">
                    <?php echo $__env->make('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5 rotate-180'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </a>
            <?php else: ?>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-300">
                    <?php echo $__env->make('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5 rotate-180'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </span>
            <?php endif; ?>
        </nav>
    </div>
<?php endif; ?><?php /**PATH C:\projects\to step 12 backup\resources\views/components/simple-paginator.blade.php ENDPATH**/ ?>