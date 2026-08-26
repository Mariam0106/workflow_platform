<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <h1 class="text-lg font-semibold text-brand-navy">Dashboard</h1>
        <p class="mt-0.5 text-[13px] text-slate-400"><?php echo e($user->activeApplicationRole()?->name); ?> · <?php echo e($user->department?->name); ?> · <?php echo e($user->entity?->name); ?></p>
    </div>

    
    <?php
        // Classes Tailwind toujours écrites en toutes lettres (jamais
        // interpolées) - le scanner de contenu de Tailwind (build Vite)
        // repère les noms de classes en cherchant des sous-chaînes
        // littérales dans le fichier source ; "sm:grid-cols-{{ $n }}"
        // ne matcherait rien du tout au build.
        $gridColsClass = match (count($cards)) {
            3 => 'sm:grid-cols-3',
            6 => 'sm:grid-cols-3',
            default => 'sm:grid-cols-4',
        };
    ?>
    <div class="grid grid-cols-2 gap-4 <?php echo e($gridColsClass); ?>">
        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginala4ae059936bc185e758290466e2179c1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala4ae059936bc185e758290466e2179c1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi-card','data' => ['label' => $card['label'],'value' => $card['value'],'icon' => $card['icon'],'accent' => $card['accent'],'href' => $card['href'] ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($card['label']),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($card['value']),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($card['icon']),'accent' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($card['accent']),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($card['href'] ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala4ae059936bc185e758290466e2179c1)): ?>
<?php $attributes = $__attributesOriginala4ae059936bc185e758290466e2179c1; ?>
<?php unset($__attributesOriginala4ae059936bc185e758290466e2179c1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala4ae059936bc185e758290466e2179c1)): ?>
<?php $component = $__componentOriginala4ae059936bc185e758290466e2179c1; ?>
<?php unset($__componentOriginala4ae059936bc185e758290466e2179c1); ?>
<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">

        
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'lg:col-span-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-1']); ?>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Mes actions</h2>
            <ul class="space-y-1">
                <?php
                    $actions = match ($activeRole) {
                        \App\Enums\ApplicationRoleCode::Administrator => [
                            ['label' => 'Ajouter un utilisateur', 'icon' => 'users', 'href' => route('organisation.users.create')],
                            ['label' => 'Ajouter un département', 'icon' => 'building', 'href' => route('organisation.departments.create')],
                            ['label' => 'Ajouter une entité', 'icon' => 'layers', 'href' => route('organisation.entities.create')],
                        ],
                        \App\Enums\ApplicationRoleCode::Validator => [
                            ['label' => 'Mes validations', 'icon' => 'check', 'href' => route('workflow.my-validations.index')],
                        ],
                        default => [
                            ['label' => 'Nouvelle demande', 'icon' => 'plus', 'href' => route('workflow.my-requests.select-form')],
                            ['label' => 'Mes demandes', 'icon' => 'inbox', 'href' => route('workflow.my-requests.index')],
                        ],
                    };
                ?>
                <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e($action['href']); ?>" class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[13px] font-medium text-slate-600 transition hover:bg-slate-50 hover:text-brand-navy">
                            <?php echo $__env->make('layouts.partials.icon', ['name' => $action['icon'], 'class' => 'h-[18px] w-[18px] text-slate-400'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php echo e($action['label']); ?>

                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

        
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'lg:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-2']); ?>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Activité récente</h2>

            <?php $__empty_1 = true; $__currentLoopData = $recentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $notificationHref = null;
                    if ($notification->request) {
                        $notificationHref = $notification->request->requester_id === $user->id
                            ? route('workflow.my-requests.show', $notification->request)
                            : route('workflow.my-validations.show', $notification->request);
                    }
                ?>
                <<?php echo e($notificationHref ? 'a' : 'div'); ?>

                    <?php if($notificationHref): ?> href="<?php echo e($notificationHref); ?>" <?php endif; ?>
                    class="group flex items-start gap-3 border-b border-brand-border py-3 last:border-0 last:pb-0 <?php echo e($notificationHref ? 'transition hover:bg-slate-50/60 -mx-2 px-2 rounded-lg' : ''); ?>"
                >
                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-blue/10 text-brand-blue">
                        <?php echo $__env->make('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-3.5 w-3.5'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-brand-navy <?php echo e($notificationHref ? 'group-hover:text-brand-blue' : ''); ?>"><?php echo e($notification->title); ?></p>
                        <p class="truncate text-xs text-slate-500"><?php echo e($notification->message); ?></p>
                    </div>
                    <span class="shrink-0 text-xs text-slate-400"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                </<?php echo e($notificationHref ? 'a' : 'div'); ?>>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bell','title' => 'Aucune activité pour le moment']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bell','title' => 'Aucune activité pour le moment']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
            <?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Tableau de bord'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projects\to step 12 backup\resources\views/dashboard.blade.php ENDPATH**/ ?>