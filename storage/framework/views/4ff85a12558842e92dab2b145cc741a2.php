<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $workflow->name,'description' => $workflow->code . ' · ' . $workflow->displayVersion()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflow->name),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflow->code . ' · ' . $workflow->displayVersion())]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if($workflow->isDraft()): ?>
                <?php if (isset($component)) { $__componentOriginald5ef95010bab1b9161871d6deae199ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5ef95010bab1b9161871d6deae199ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.publish', $workflow),'confirm' => 'Publier « ' . ($workflow->name) . ' » ? Il deviendra utilisable par des formulaires et ne sera plus modifiable.','variant' => 'primary','icon' => 'check']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.publish', $workflow)),'confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Publier « ' . ($workflow->name) . ' » ? Il deviendra utilisable par des formulaires et ne sera plus modifiable.'),'variant' => 'primary','icon' => 'check']); ?>Publier <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.duplicate', $workflow),'confirm' => 'Dupliquer « ' . ($workflow->name) . ' » (étapes et transitions incluses) en un nouveau workflow indépendant ?','variant' => 'secondary','icon' => 'file']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.duplicate', $workflow)),'confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Dupliquer « ' . ($workflow->name) . ' » (étapes et transitions incluses) en un nouveau workflow indépendant ?'),'variant' => 'secondary','icon' => 'file']); ?>Dupliquer <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.archive', $workflow),'confirm' => 'Archiver « ' . ($workflow->name) . ' » ? Il ne pourra plus être associé à de nouveaux formulaires.','variant' => 'danger','icon' => 'archive']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.archive', $workflow)),'confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Archiver « ' . ($workflow->name) . ' » ? Il ne pourra plus être associé à de nouveaux formulaires.'),'variant' => 'danger','icon' => 'archive']); ?>Archiver <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.destroy', $workflow),'method' => 'DELETE','confirm' => 'Supprimer définitivement « ' . ($workflow->name) . ' » ? Cette action est irréversible.','variant' => 'danger','icon' => 'trash']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.destroy', $workflow)),'method' => 'DELETE','confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Supprimer définitivement « ' . ($workflow->name) . ' » ? Cette action est irréversible.'),'variant' => 'danger','icon' => 'trash']); ?>Supprimer <?php echo $__env->renderComponent(); ?>
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
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.workflows.index')).'','variant' => 'secondary','icon' => 'arrow-left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.workflows.index')).'','variant' => 'secondary','icon' => 'arrow-left']); ?>Retour <?php echo $__env->renderComponent(); ?>
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
        <?php if (isset($component)) { $__componentOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f = $component; } ?>
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
<?php endif; ?>
    </div>

    <?php if (! ($workflow->isDraft())): ?>
        <div class="mb-6 flex items-start gap-2.5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <?php echo $__env->make('layouts.partials.icon', ['name' => 'alert', 'class' => 'mt-0.5 h-4 w-4 shrink-0'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <span>
                Ce workflow est <?php echo e($workflow->isPublished() ? 'publié' : 'archivé'); ?> et n'est donc plus modifiable.
                <?php if($workflow->isPublished()): ?>
                    Utilisez « Dupliquer » pour repartir d'une copie éditable.
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    
    <?php if($workflow->forms->isNotEmpty()): ?>
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'mb-4','padded' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-4','padded' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
            <div class="border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Formulaire(s) lié(s) à ce workflow</h2>
            </div>
            <ul class="divide-y divide-brand-border">
                <?php $__currentLoopData = $workflow->forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $linkedForm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center gap-3 px-5 py-3">
                        <div class="min-w-0 flex-1">
                            <a href="<?php echo e(route('workflow.admin.forms.edit', $linkedForm)); ?>" class="truncate text-sm font-medium text-brand-navy hover:text-brand-blue">
                                <?php echo e($linkedForm->name); ?>

                            </a>
                            <p class="truncate text-xs text-slate-400"><?php echo e($linkedForm->code); ?> · <?php echo e($linkedForm->formFields()->count()); ?> champ(s)</p>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.lifecycle-badge','data' => ['status' => $linkedForm->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lifecycle-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($linkedForm->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f)): ?>
<?php $attributes = $__attributesOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f; ?>
<?php unset($__attributesOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f)): ?>
<?php $component = $__componentOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f; ?>
<?php unset($__componentOriginal9d51cfeeacfcaee4a103f8f2c3e1f05f); ?>
<?php endif; ?>
                        <?php if($workflow->isPublished() && $linkedForm->isDraft()): ?>
                            <?php if (isset($component)) { $__componentOriginald5ef95010bab1b9161871d6deae199ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5ef95010bab1b9161871d6deae199ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.forms.publish', $linkedForm),'confirm' => 'Publier « ' . $linkedForm->name . ' » ? Il deviendra utilisable pour créer des demandes.','variant' => 'primary','icon' => 'check']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.forms.publish', $linkedForm)),'confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Publier « ' . $linkedForm->name . ' » ? Il deviendra utilisable pour créer des demandes.'),'variant' => 'primary','icon' => 'check']); ?>Publier <?php echo $__env->renderComponent(); ?>
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
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php if($workflow->isPublished() && $workflow->forms->contains(fn ($f) => $f->isDraft())): ?>
                <div class="flex items-start gap-2.5 border-t border-brand-border bg-amber-50/60 px-5 py-3 text-[13px] text-amber-800">
                    <?php echo $__env->make('layouts.partials.icon', ['name' => 'alert', 'class' => 'mt-0.5 h-4 w-4 shrink-0'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <span>Ce workflow est publié, mais au moins un formulaire ci-dessus est encore en brouillon - publie-le aussi pour qu'il soit réellement utilisable.</span>
                </div>
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
    <?php endif; ?>

    
    <div class="space-y-4">
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
            <div class="flex items-center justify-between border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">
                    Étapes (<?php echo e($workflow->workflowSteps->count()); ?>)
                </h2>
                <?php if($workflow->isDraft()): ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.workflows.steps.create', $workflow)).'','size' => 'sm','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.workflows.steps.create', $workflow)).'','size' => 'sm','icon' => 'plus']); ?>Ajouter une étape <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if($workflow->workflowSteps->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'branch','title' => 'Aucune étape','description' => 'Un workflow doit contenir au moins une étape, dont une de début et une de fin.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'branch','title' => 'Aucune étape','description' => 'Un workflow doit contenir au moins une étape, dont une de début et une de fin.']); ?>
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
                <?php if($workflow->isDraft()): ?>
                    <p class="border-b border-brand-border bg-slate-50/60 px-5 py-2 text-xs text-slate-500">
                        Glissez une étape par sa poignée <span class="inline-block px-0.5">⠿</span> pour la réordonner.
                    </p>
                <?php endif; ?>
                <ul id="workflow-steps-list" class="divide-y divide-brand-border">
                    <?php $__currentLoopData = $workflow->workflowSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center gap-3 px-5 py-3 <?php echo e($workflow->isDraft() ? 'cursor-grab active:cursor-grabbing' : ''); ?>"
                            <?php if($workflow->isDraft()): ?> draggable="true" data-step-id="<?php echo e($step->id); ?>" <?php endif; ?>>
                            <?php if($workflow->isDraft()): ?>
                                <span class="shrink-0 select-none text-slate-300" title="Glisser pour réordonner">⠿⠿</span>
                            <?php endif; ?>
                            <div class="min-w-0 flex-1">
                                <p class="flex items-center gap-1.5 truncate text-sm font-medium text-brand-navy">
                                    <?php echo e($step->step_order); ?>. <?php echo e($step->name); ?>

                                    <?php if($step->is_start): ?> <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => 'blue']); ?>Début <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?> <?php endif; ?>
                                    <?php if($step->is_end): ?> <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => 'success']); ?>Fin <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?> <?php endif; ?>
                                </p>
                                <p class="truncate text-xs text-slate-400">
                                    <?php echo e($step->code); ?> ·
                                    <?php switch($step->validator_type->value):
                                        case ('ROLE'): ?> Validateur : Rôle Applicatif <?php break; ?>
                                        <?php case ('BUSINESS_FUNCTION'): ?> Validateur : Fonction Métier <?php break; ?>
                                        <?php case ('USER'): ?> Validateur : Utilisateur désigné <?php break; ?>
                                        <?php case ('N_PLUS_1'): ?> Validateur : Responsable direct (N+1) <?php break; ?>
                                        <?php case ('ENTITY_MANAGER'): ?> Validateur : Responsable d'Entité <?php break; ?>
                                        <?php case ('DEPARTMENT_MANAGER'): ?> Validateur : Responsable de Département <?php break; ?>
                                    <?php endswitch; ?>
                                    <?php if($step->validatorReferenceLabel()): ?>
                                        — <?php echo e($step->validatorReferenceLabel()); ?>

                                    <?php endif; ?>
                                </p>
                            </div>

                            <?php if($workflow->isDraft()): ?>
                                <div class="flex shrink-0 items-center gap-1">
                                    <?php if (! ($step->is_start)): ?>
                                        <form method="POST" action="<?php echo e(route('workflow.admin.workflows.steps.set-start', [$workflow, $step])); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'ghost','size' => 'sm']); ?>Définir début <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('workflow.admin.workflows.steps.move-up', [$workflow, $step])); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'ghost','size' => 'sm','icon' => 'chevron-down','class' => 'rotate-180','title' => 'Monter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'ghost','size' => 'sm','icon' => 'chevron-down','class' => 'rotate-180','title' => 'Monter']); ?><span class="sr-only">Monter</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('workflow.admin.workflows.steps.move-down', [$workflow, $step])); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'ghost','size' => 'sm','icon' => 'chevron-down','title' => 'Descendre']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'ghost','size' => 'sm','icon' => 'chevron-down','title' => 'Descendre']); ?><span class="sr-only">Descendre</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                                    </form>
                                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.workflows.steps.edit', [$workflow, $step])).'','variant' => 'ghost','size' => 'sm','icon' => 'edit','title' => 'Modifier']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.workflows.steps.edit', [$workflow, $step])).'','variant' => 'ghost','size' => 'sm','icon' => 'edit','title' => 'Modifier']); ?><span class="sr-only">Modifier</span> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.steps.destroy', [$workflow, $step]),'method' => 'DELETE','confirm' => 'Supprimer l\'étape « ' . ($step->name) . ' » ?','variant' => 'ghost','icon' => 'trash','title' => 'Supprimer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.steps.destroy', [$workflow, $step])),'method' => 'DELETE','confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Supprimer l\'étape « ' . ($step->name) . ' » ?'),'variant' => 'ghost','icon' => 'trash','title' => 'Supprimer']); ?><span class="sr-only">Supprimer</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $attributes = $__attributesOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__attributesOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $component = $__componentOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__componentOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
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
            <div class="flex items-center justify-between border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">
                    Transitions (<?php echo e($workflow->workflowSteps->sum(fn ($s) => $s->outgoingTransitions->count())); ?>)
                </h2>
                <?php if($workflow->isDraft()): ?>
                    <?php if($workflow->workflowSteps->count() < 2): ?>
                        <span class="text-xs text-slate-400">Ajoutez au moins 2 étapes pour créer une transition.</span>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.workflows.transitions.create', $workflow)).'','size' => 'sm','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.workflows.transitions.create', $workflow)).'','size' => 'sm','icon' => 'plus']); ?>Ajouter une transition <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if($workflow->forms_count === 0): ?>
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-brand-border bg-amber-50/60 px-5 py-3 text-[13px] text-amber-800">
                    <span>
                        Les conditions d'une transition se basent sur les champs d'un Formulaire - aucun Formulaire n'utilise encore ce Workflow.
                    </span>
                    <div class="flex shrink-0 items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.forms.create', ['workflow' => $workflow->id])).'','size' => 'sm','variant' => 'secondary','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.forms.create', ['workflow' => $workflow->id])).'','size' => 'sm','variant' => 'secondary','icon' => 'plus']); ?>
                            Créer un formulaire pour ce workflow
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                        <?php if($existingForms->isNotEmpty()): ?>
                            <button type="button" onclick="document.getElementById('form-from-existing-panel').classList.toggle('hidden')"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-1.5 text-[13px] font-semibold text-brand-navy underline decoration-dotted underline-offset-4 transition hover:text-brand-blue">
                                ou repartir d'un formulaire existant
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($existingForms->isNotEmpty()): ?>
                    
                    <form id="form-from-existing-panel" method="POST" action="<?php echo e(route('workflow.admin.workflows.forms-from-existing', $workflow)); ?>"
                          class="hidden space-y-3 border-b border-brand-border bg-slate-50/60 px-5 py-4">
                        <?php echo csrf_field(); ?>
                        <?php if (isset($component)) { $__componentOriginal67ad07a4b593e690d435fee92e6413bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67ad07a4b593e690d435fee92e6413bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-select','data' => ['name' => 'source_form_id','label' => 'Formulaire à reprendre','options' => $existingForms,'placeholder' => '—','required' => true,'hint' => 'Ses champs seront copiés dans un nouveau formulaire, dédié à ce workflow - le formulaire d\'origine garde son propre workflow, inchangé.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'source_form_id','label' => 'Formulaire à reprendre','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($existingForms),'placeholder' => '—','required' => true,'hint' => 'Ses champs seront copiés dans un nouveau formulaire, dédié à ce workflow - le formulaire d\'origine garde son propre workflow, inchangé.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67ad07a4b593e690d435fee92e6413bb)): ?>
<?php $attributes = $__attributesOriginal67ad07a4b593e690d435fee92e6413bb; ?>
<?php unset($__attributesOriginal67ad07a4b593e690d435fee92e6413bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67ad07a4b593e690d435fee92e6413bb)): ?>
<?php $component = $__componentOriginal67ad07a4b593e690d435fee92e6413bb; ?>
<?php unset($__componentOriginal67ad07a4b593e690d435fee92e6413bb); ?>
<?php endif; ?>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['name' => 'name','label' => 'Nom du nouveau formulaire','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Nom du nouveau formulaire','required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['name' => 'code','label' => 'Code du nouveau formulaire','required' => true,'hint' => 'Doit être différent du formulaire d\'origine.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'code','label' => 'Code du nouveau formulaire','required' => true,'hint' => 'Doit être différent du formulaire d\'origine.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                        </div>
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','size' => 'sm','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','icon' => 'plus']); ?>Créer ce formulaire <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

            <?php
                $allTransitions = $workflow->workflowSteps->flatMap(fn ($s) => $s->outgoingTransitions);
            ?>

            <?php if($allTransitions->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'branch','title' => 'Aucune transition','description' => 'Les transitions relient les étapes entre elles et déterminent le chemin suivi par une demande.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'branch','title' => 'Aucune transition','description' => 'Les transitions relient les étapes entre elles et déterminent le chemin suivi par une demande.']); ?>
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
                <ul class="divide-y divide-brand-border">
                    <?php $__currentLoopData = $allTransitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center gap-3 px-5 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-brand-navy">
                                    <?php echo e($transition->fromStep?->name); ?> → <?php echo e($transition->toStep?->name); ?>

                                    <?php if($transition->is_default): ?> <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => 'slate']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => 'slate']); ?>Par défaut <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?> <?php endif; ?>
                                </p>
                                <p class="truncate text-xs text-slate-400">
                                    <?php echo e($transition->action_name); ?> ·
                                    <?php switch($transition->priority):
                                        case (80): ?> Priorité élevée <?php break; ?>
                                        <?php case (100): ?> Priorité urgente <?php break; ?>
                                        <?php default: ?> Priorité normale
                                    <?php endswitch; ?>
                                    <?php if($transition->transitionConditions->isNotEmpty()): ?>
                                        · <?php echo e($transition->transitionConditions->count()); ?> condition(s)
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php if($workflow->isDraft()): ?>
                                <div class="flex shrink-0 items-center gap-1">
                                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.admin.workflows.transitions.edit', [$workflow, $transition])).'','variant' => 'ghost','size' => 'sm','icon' => 'edit','title' => 'Modifier']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.admin.workflows.transitions.edit', [$workflow, $transition])).'','variant' => 'ghost','size' => 'sm','icon' => 'edit','title' => 'Modifier']); ?><span class="sr-only">Modifier</span> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.transitions.destroy', [$workflow, $transition]),'method' => 'DELETE','confirm' => 'Supprimer cette transition ?','variant' => 'ghost','icon' => 'trash','title' => 'Supprimer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.transitions.destroy', [$workflow, $transition])),'method' => 'DELETE','confirm' => 'Supprimer cette transition ?','variant' => 'ghost','icon' => 'trash','title' => 'Supprimer']); ?><span class="sr-only">Supprimer</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $attributes = $__attributesOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__attributesOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $component = $__componentOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__componentOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
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

    
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'mt-4','padded' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-4','padded' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
        <div class="border-b border-brand-border px-5 py-4">
            <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Notifications de clôture</h2>
            <p class="mt-0.5 text-xs text-slate-400">Le Demandeur est toujours prévenu automatiquement. Ajoute ici qui doit l'être en plus.</p>
        </div>

        <?php if($workflow->completionNotifications->isNotEmpty()): ?>
            <ul class="divide-y divide-brand-border">
                <?php $__currentLoopData = $workflow->completionNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $completionNotification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center gap-3 px-5 py-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                            <?php echo $__env->make('layouts.partials.icon', ['name' => $completionNotification->isBusinessFunction() ? 'briefcase' : 'users', 'class' => 'h-3.5 w-3.5'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-brand-navy"><?php echo e($completionNotification->referenceLabel() ?? '—'); ?></p>
                            <p class="text-xs text-slate-400"><?php echo e($completionNotification->isBusinessFunction() ? 'Fonction Métier' : 'Utilisateur désigné'); ?></p>
                        </div>
                        <?php if (isset($component)) { $__componentOriginald5ef95010bab1b9161871d6deae199ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5ef95010bab1b9161871d6deae199ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.admin.workflows.completion-notifications.destroy', [$workflow, $completionNotification]),'method' => 'DELETE','confirm' => 'Retirer ' . ($completionNotification->referenceLabel() ?? 'ce destinataire') . ' des notifications de clôture ?','variant' => 'ghost','icon' => 'trash','title' => 'Retirer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.admin.workflows.completion-notifications.destroy', [$workflow, $completionNotification])),'method' => 'DELETE','confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Retirer ' . ($completionNotification->referenceLabel() ?? 'ce destinataire') . ' des notifications de clôture ?'),'variant' => 'ghost','icon' => 'trash','title' => 'Retirer']); ?><span class="sr-only">Retirer</span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $attributes = $__attributesOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__attributesOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5ef95010bab1b9161871d6deae199ef)): ?>
<?php $component = $__componentOriginald5ef95010bab1b9161871d6deae199ef; ?>
<?php unset($__componentOriginald5ef95010bab1b9161871d6deae199ef); ?>
<?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>

        <?php if($workflow->isDraft() || $workflow->isPublished()): ?>
            <form method="POST" action="<?php echo e(route('workflow.admin.workflows.completion-notifications.store', $workflow)); ?>" class="flex flex-wrap items-end gap-3 border-t border-brand-border p-5">
                <?php echo csrf_field(); ?>
                <div class="min-w-[10rem]">
                    <label for="notify_type" class="mb-1.5 block text-[13px] font-medium text-slate-700">Type</label>
                    <select id="notify_type" name="notify_type" onchange="updateNotifyReferenceFields()"
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <option value="BUSINESS_FUNCTION">Fonction Métier</option>
                        <option value="USER">Utilisateur désigné</option>
                    </select>
                </div>
                <div id="notify-ref-business-function" class="min-w-[12rem] flex-1">
                    <?php if (isset($component)) { $__componentOriginal67ad07a4b593e690d435fee92e6413bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67ad07a4b593e690d435fee92e6413bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-select','data' => ['name' => 'notify_reference','label' => 'Fonction Métier','options' => $businessFunctions,'placeholder' => '—']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_reference','label' => 'Fonction Métier','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($businessFunctions),'placeholder' => '—']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67ad07a4b593e690d435fee92e6413bb)): ?>
<?php $attributes = $__attributesOriginal67ad07a4b593e690d435fee92e6413bb; ?>
<?php unset($__attributesOriginal67ad07a4b593e690d435fee92e6413bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67ad07a4b593e690d435fee92e6413bb)): ?>
<?php $component = $__componentOriginal67ad07a4b593e690d435fee92e6413bb; ?>
<?php unset($__componentOriginal67ad07a4b593e690d435fee92e6413bb); ?>
<?php endif; ?>
                </div>
                <div id="notify-ref-user" class="hidden min-w-[12rem] flex-1">
                    <?php if (isset($component)) { $__componentOriginal1a18ca72d0c50d7639c2303989015352 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1a18ca72d0c50d7639c2303989015352 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.user-picker','data' => ['name' => 'notify_reference','label' => 'Utilisateur','users' => $users,'entities' => $entities,'departments' => $departments]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('user-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_reference','label' => 'Utilisateur','users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'entities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($entities),'departments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($departments)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1a18ca72d0c50d7639c2303989015352)): ?>
<?php $attributes = $__attributesOriginal1a18ca72d0c50d7639c2303989015352; ?>
<?php unset($__attributesOriginal1a18ca72d0c50d7639c2303989015352); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1a18ca72d0c50d7639c2303989015352)): ?>
<?php $component = $__componentOriginal1a18ca72d0c50d7639c2303989015352; ?>
<?php unset($__componentOriginal1a18ca72d0c50d7639c2303989015352); ?>
<?php endif; ?>
                </div>
                <script>
                    function updateNotifyReferenceFields() {
                        var isBiz = document.getElementById('notify_type').value === 'BUSINESS_FUNCTION';
                        var bizWrap = document.getElementById('notify-ref-business-function');
                        var userWrap = document.getElementById('notify-ref-user');
                        bizWrap.classList.toggle('hidden', !isBiz);
                        bizWrap.querySelector('select').disabled = !isBiz;
                        userWrap.classList.toggle('hidden', isBiz);
                        userWrap.querySelector('input[data-role="value"]').disabled = isBiz;
                    }
                    updateNotifyReferenceFields();
                </script>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','size' => 'sm','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','icon' => 'plus']); ?>Ajouter <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            </form>
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

    
    <details class="group mt-4 overflow-hidden rounded-xl border border-brand-border bg-white">
        <summary class="flex cursor-pointer list-none items-center justify-between px-5 py-4 [&::-webkit-details-marker]:hidden">
            <div>
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Informations</h2>
                <p class="mt-0.5 truncate text-xs text-slate-400"><?php echo e($workflow->workflowCategory?->name ?? '—'); ?></p>
            </div>
            <span class="flex items-center gap-1.5 text-[13px] font-medium text-brand-blue">
                Modifier
                <svg viewBox="0 0 18 18" fill="none" class="h-3.5 w-3.5 transition-transform group-open:rotate-180">
                    <path d="M4.5 7l4.5 4.5L13.5 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </summary>

        <div class="border-t border-brand-border px-5 py-4">
            <?php if($workflow->isDraft()): ?>
                <form method="POST" action="<?php echo e(route('workflow.admin.workflows.update', $workflow)); ?>" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['name' => 'name','label' => 'Nom','required' => true,'value' => $workflow->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Nom','required' => true,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflow->name)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['name' => 'code','label' => 'Code','required' => true,'value' => $workflow->code]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'code','label' => 'Code','required' => true,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflow->code)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal67ad07a4b593e690d435fee92e6413bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67ad07a4b593e690d435fee92e6413bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-select','data' => ['name' => 'workflow_category_id','label' => 'Catégorie','options' => $workflowCategories,'required' => true,'value' => $workflow->workflow_category_id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'workflow_category_id','label' => 'Catégorie','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowCategories),'required' => true,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflow->workflow_category_id)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67ad07a4b593e690d435fee92e6413bb)): ?>
<?php $attributes = $__attributesOriginal67ad07a4b593e690d435fee92e6413bb; ?>
<?php unset($__attributesOriginal67ad07a4b593e690d435fee92e6413bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67ad07a4b593e690d435fee92e6413bb)): ?>
<?php $component = $__componentOriginal67ad07a4b593e690d435fee92e6413bb; ?>
<?php unset($__componentOriginal67ad07a4b593e690d435fee92e6413bb); ?>
<?php endif; ?>
                    <div class="sm:col-span-2">
                        <?php if (isset($component)) { $__componentOriginalcc0154580828f80bdab5d7fe416ed74a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcc0154580828f80bdab5d7fe416ed74a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-textarea','data' => ['name' => 'description','label' => 'Description','value' => $workflow->description]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'description','label' => 'Description','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflow->description)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcc0154580828f80bdab5d7fe416ed74a)): ?>
<?php $attributes = $__attributesOriginalcc0154580828f80bdab5d7fe416ed74a; ?>
<?php unset($__attributesOriginalcc0154580828f80bdab5d7fe416ed74a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcc0154580828f80bdab5d7fe416ed74a)): ?>
<?php $component = $__componentOriginalcc0154580828f80bdab5d7fe416ed74a; ?>
<?php unset($__componentOriginalcc0154580828f80bdab5d7fe416ed74a); ?>
<?php endif; ?>
                    </div>
                    <div class="sm:col-span-2">
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','size' => 'sm','icon' => 'check']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','icon' => 'check']); ?>Enregistrer <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    </div>
                </form>
            <?php else: ?>
                <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-slate-400">Catégorie</dt>
                        <dd class="mt-0.5 text-brand-navy"><?php echo e($workflow->workflowCategory?->name); ?></dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-slate-400">Description</dt>
                        <dd class="mt-0.5 text-brand-navy"><?php echo e($workflow->description ?? '—'); ?></dd>
                    </div>
                </dl>
            <?php endif; ?>
        </div>
    </details>

    <?php if($workflow->isDraft() && $workflow->workflowSteps->count() > 1): ?>
        <form id="reorder-steps-form" method="POST" action="<?php echo e(route('workflow.admin.workflows.steps.reorder', $workflow)); ?>" class="hidden">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="ordered_ids" id="reorder-steps-input">
        </form>
        <script>
            (function () {
                var list = document.getElementById('workflow-steps-list');
                if (!list) return;
                var dragged = null;

                list.querySelectorAll('li[draggable="true"]').forEach(function (li) {
                    li.addEventListener('dragstart', function () {
                        dragged = li;
                        li.classList.add('opacity-40');
                    });
                    li.addEventListener('dragend', function () {
                        li.classList.remove('opacity-40');
                    });
                    li.addEventListener('dragover', function (event) {
                        event.preventDefault();
                    });
                    li.addEventListener('drop', function (event) {
                        event.preventDefault();
                        if (!dragged || dragged === li) return;

                        var rect = li.getBoundingClientRect();
                        var insertBefore = (event.clientY - rect.top) < rect.height / 2;
                        list.insertBefore(dragged, insertBefore ? li : li.nextSibling);

                        var ids = Array.prototype.map.call(
                            list.querySelectorAll('li[draggable="true"]'),
                            function (item) { return item.dataset.stepId; }
                        );
                        document.getElementById('reorder-steps-input').value = ids.join(',');
                        document.getElementById('reorder-steps-form').submit();
                    });
                });
            })();
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => $workflow->name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projects\to step 12 backup\resources\views/workflow/workflows/edit.blade.php ENDPATH**/ ?>