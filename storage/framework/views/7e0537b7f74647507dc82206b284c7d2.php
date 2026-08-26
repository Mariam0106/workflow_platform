<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Workflows','description' => ''.e($workflows->total()).' workflow(s)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Workflows','description' => ''.e($workflows->total()).' workflow(s)']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.workflow-categories.index')).'','variant' => 'secondary','icon' => 'layers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.workflow-categories.index')).'','variant' => 'secondary','icon' => 'layers']); ?>Catégories <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.workflows.create')).'','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.workflows.create')).'','icon' => 'plus']); ?>Nouveau workflow <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

    <div class="mb-4">
        <?php if (isset($component)) { $__componentOriginal1c4b45f62348de9b6fa41ee823d3fa96 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c4b45f62348de9b6fa41ee823d3fa96 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-input','data' => ['value' => $search,'placeholder' => 'Rechercher un workflow…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'placeholder' => 'Rechercher un workflow…']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1c4b45f62348de9b6fa41ee823d3fa96)): ?>
<?php $attributes = $__attributesOriginal1c4b45f62348de9b6fa41ee823d3fa96; ?>
<?php unset($__attributesOriginal1c4b45f62348de9b6fa41ee823d3fa96); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1c4b45f62348de9b6fa41ee823d3fa96)): ?>
<?php $component = $__componentOriginal1c4b45f62348de9b6fa41ee823d3fa96; ?>
<?php unset($__componentOriginal1c4b45f62348de9b6fa41ee823d3fa96); ?>
<?php endif; ?>
    </div>

    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['padded' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['padded' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
        <?php if($workflows->isEmpty()): ?>
            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'branch','title' => 'Aucun workflow','description' => 'Créez votre premier circuit de validation.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'branch','title' => 'Aucun workflow','description' => 'Créez votre premier circuit de validation.']); ?>
                 <?php $__env->slot('actions', null, []); ?> 
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.workflows.create')).'','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.workflows.create')).'','icon' => 'plus']); ?>Nouveau workflow <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                 <?php $__env->endSlot(); ?>
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
        <?php else: ?>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Workflow</th>
                        <th class="px-5 py-3">Catégorie</th>
                        <th class="px-5 py-3">Étapes</th>
                        <th class="px-5 py-3">Formulaires liés</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    <?php $__currentLoopData = $workflows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workflow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <a href="<?php echo e(route('workflow.admin.workflows.edit', $workflow)); ?>" class="font-medium text-brand-navy hover:text-brand-blue"><?php echo e($workflow->name); ?></a>
                                <span class="block text-xs text-slate-400"><?php echo e($workflow->code); ?> · <?php echo e($workflow->displayVersion()); ?></span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600"><?php echo e($workflow->workflowCategory?->name); ?></td>
                            <td class="px-5 py-3.5 text-slate-600"><?php echo e($workflow->workflow_steps_count); ?></td>
                            <td class="px-5 py-3.5 text-slate-600"><?php echo e($workflow->forms_count); ?></td>
                            <td class="px-5 py-3.5"><?php if (isset($component)) { $__componentOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.lifecycle-badge','data' => ['status' => $workflow->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lifecycle-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflow->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f)): ?>
<?php $attributes = $__attributesOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f; ?>
<?php unset($__attributesOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f)): ?>
<?php $component = $__componentOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f; ?>
<?php unset($__componentOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f); ?>
<?php endif; ?></td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if($workflow->isDraft()): ?>
                                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.workflows.edit', $workflow)).'','variant' => 'ghost','size' => 'sm','icon' => 'edit','title' => 'Modifier']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.workflows.edit', $workflow)).'','variant' => 'ghost','size' => 'sm','icon' => 'edit','title' => 'Modifier']); ?><span class="sr-only">Modifier</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                                        <?php if (isset($component)) { $__componentOriginald5ef95010bab1b9161871d6deae199ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5ef95010bab1b9161871d6deae199ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.publish', $workflow),'confirm' => 'Publier « ' . ($workflow->name) . ' » ? Il deviendra utilisable par des formulaires et ne sera plus modifiable.','variant' => 'ghost','icon' => 'check','title' => 'Publier']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.publish', $workflow)),'confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Publier « ' . ($workflow->name) . ' » ? Il deviendra utilisable par des formulaires et ne sera plus modifiable.'),'variant' => 'ghost','icon' => 'check','title' => 'Publier']); ?><span class="sr-only">Publier</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $attributes = $__attributesOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__attributesOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $component = $__componentOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__componentOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (isset($component)) { $__componentOriginald5ef95010bab1b9161871d6deae199ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5ef95010bab1b9161871d6deae199ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.duplicate', $workflow),'confirm' => 'Dupliquer « ' . ($workflow->name) . ' » (étapes et transitions incluses) en un nouveau workflow indépendant ?','variant' => 'ghost','icon' => 'file','title' => 'Dupliquer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.duplicate', $workflow)),'confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Dupliquer « ' . ($workflow->name) . ' » (étapes et transitions incluses) en un nouveau workflow indépendant ?'),'variant' => 'ghost','icon' => 'file','title' => 'Dupliquer']); ?><span class="sr-only">Dupliquer</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $attributes = $__attributesOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__attributesOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $component = $__componentOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__componentOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
                                    <?php if(! $workflow->isArchived()): ?>
                                        <?php if (isset($component)) { $__componentOriginald5ef95010bab1b9161871d6deae199ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5ef95010bab1b9161871d6deae199ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.archive', $workflow),'confirm' => 'Archiver « ' . ($workflow->name) . ' » ? Il ne pourra plus être associé à de nouveaux formulaires.','variant' => 'ghost','icon' => 'archive','title' => 'Archiver']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.archive', $workflow)),'confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Archiver « ' . ($workflow->name) . ' » ? Il ne pourra plus être associé à de nouveaux formulaires.'),'variant' => 'ghost','icon' => 'archive','title' => 'Archiver']); ?><span class="sr-only">Archiver</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $attributes = $__attributesOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__attributesOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $component = $__componentOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__componentOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
                                    <?php endif; ?>
                                    <?php if($workflow->isDraft()): ?>
                                        <?php if (isset($component)) { $__componentOriginald5ef95010bab1b9161871d6deae199ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5ef95010bab1b9161871d6deae199ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.destroy', $workflow),'method' => 'DELETE','confirm' => 'Supprimer définitivement « ' . ($workflow->name) . ' » ? Cette action est irréversible.','variant' => 'ghost','icon' => 'trash','title' => 'Supprimer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.destroy', $workflow)),'method' => 'DELETE','confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Supprimer définitivement « ' . ($workflow->name) . ' » ? Cette action est irréversible.'),'variant' => 'ghost','icon' => 'trash','title' => 'Supprimer']); ?><span class="sr-only">Supprimer</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $attributes = $__attributesOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__attributesOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $component = $__componentOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__componentOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?php if (isset($component)) { $__componentOriginalfd05ae373d147fba9d8600782649a7b0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfd05ae373d147fba9d8600782649a7b0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.simple-paginator','data' => ['paginator' => $workflows]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('simple-paginator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflows)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfd05ae373d147fba9d8600782649a7b0)): ?>
<?php $attributes = $__attributesOriginalfd05ae373d147fba9d8600782649a7b0; ?>
<?php unset($__attributesOriginalfd05ae373d147fba9d8600782649a7b0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfd05ae373d147fba9d8600782649a7b0)): ?>
<?php $component = $__componentOriginalfd05ae373d147fba9d8600782649a7b0; ?>
<?php unset($__componentOriginalfd05ae373d147fba9d8600782649a7b0); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Workflows'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projects\to step 12 backup\resources\views/workflow/workflows/index.blade.php ENDPATH**/ ?>