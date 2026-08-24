<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $form->name,'description' => $form->description]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($form->name),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($form->description)]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.my-requests.select-form')).'','variant' => 'secondary','icon' => 'arrow-left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.my-requests.select-form')).'','variant' => 'secondary','icon' => 'arrow-left']); ?>Retour <?php echo $__env->renderComponent(); ?>
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

    <?php if($draft && $draftValues->isNotEmpty()): ?>
        <div class="mb-4 flex items-center gap-2.5 rounded-lg border border-brand-blue/20 bg-brand-blue/[0.04] px-4 py-3 text-[13px] text-brand-navy">
            <?php echo $__env->make('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-4 w-4 shrink-0 text-brand-blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <span>Brouillon repris — dernière sauvegarde automatique à <?php echo e($draft->updated_at->format('H:i')); ?>.</span>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('workflow.my-requests.store', $form)); ?>" id="request-form" enctype="multipart/form-data" class="max-w-2xl space-y-4">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="form_id" value="<?php echo e($form->id); ?>">

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
            <div id="wizard-progress" class="hidden items-center justify-between border-b border-brand-border px-6 py-3.5 text-[13px] text-slate-500">
                <span id="wizard-progress-label"></span>
                <div id="wizard-progress-dots" class="flex items-center gap-1.5"></div>
            </div>

            <div class="p-6">
                <?php
                    $previousSection = null;
                    $pageIndex = -1;
                    $pageTitles = [];
                ?>
                <?php $__currentLoopData = $form->formFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $inputName = "values[{$field->id}]";
                        $oldKey = "values.{$field->id}";
                        $draftValue = $draftValues->get($field->id);
                        $startsNewPage = $field->section_title && $field->section_title !== $previousSection;
                    ?>

                    <?php if($startsNewPage): ?>
                        <?php if($pageIndex >= 0): ?> </div> <?php endif; ?>
                        <?php $pageIndex++; $pageTitles[] = $field->section_title; ?>
                        <div class="wizard-page space-y-4" data-page="<?php echo e($pageIndex); ?>" <?php if($pageIndex > 0): ?> style="display:none" <?php endif; ?>>
                    <?php elseif($pageIndex === -1): ?>
                        <?php $pageIndex = 0; $pageTitles[] = null; ?>
                        <div class="wizard-page space-y-4" data-page="0">
                    <?php endif; ?>
                    <?php $previousSection = $field->section_title ?? $previousSection; ?>

                    <?php if($field->isFile()): ?>
                        <div>
                            <label for="<?php echo e($inputName); ?>" class="mb-1.5 block text-[13px] font-medium text-slate-700">
                                <?php echo e($field->label); ?> <?php if($field->is_required): ?> <span class="text-brand-danger">*</span> <?php endif; ?>
                            </label>
                            <input id="<?php echo e($inputName); ?>" type="file" name="<?php echo e($inputName); ?>" <?php if($field->is_required): ?> required <?php endif; ?>
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                   class="block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-[13px] file:font-medium file:text-brand-navy hover:file:bg-slate-200 focus:outline-none focus:ring-4 focus:ring-brand-blue/10 <?php echo e($errors->has($inputName) ? 'border-red-300 focus:border-red-400 focus:ring-red-100' : 'border-brand-border focus:border-brand-blue focus:ring-brand-blue/10'); ?>">
                            <p class="mt-1 text-xs text-slate-400">PDF, image, Word ou Excel — 10 Mo max. Envoyé avec la demande (pas sauvegardé dans le brouillon automatique).</p>
                            <?php $__errorArgs = [$inputName];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-brand-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    <?php elseif($field->isSelect()): ?>
                        <?php
                            $freeTextOption = $field->fieldOptions->first(fn ($o) => $o->isFreeText());
                            $realValues = $field->fieldOptions->reject(fn ($o) => $o->isFreeText())->pluck('value');
                            $defaultValue = optional($field->fieldOptions->firstWhere('is_default', true))->value;
                            $currentValue = old($oldKey, $draftValue ?? $defaultValue);
                            $isOther = $freeTextOption && (
                                $currentValue === \App\Models\FieldOption::FREE_TEXT_VALUE
                                || ($currentValue !== null && ! $realValues->contains($currentValue))
                            );
                            $selectValue = $isOther ? \App\Models\FieldOption::FREE_TEXT_VALUE : $currentValue;
                            $freeTextValue = $isOther && $currentValue !== \App\Models\FieldOption::FREE_TEXT_VALUE ? $currentValue : '';
                        ?>
                        <div>
                            <?php if (isset($component)) { $__componentOriginal67ad07a4b593e690d435fee92e6413bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67ad07a4b593e690d435fee92e6413bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-select','data' => ['name' => $inputName,'label' => $field->label,'required' => $field->is_required,'options' => $field->fieldOptions,'valueKey' => 'value','labelKey' => 'label','value' => $selectValue,'dataOtherSelect' => $freeTextOption ? $inputName : null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inputName),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->label),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->is_required),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->fieldOptions),'valueKey' => 'value','labelKey' => 'label','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectValue),'data-other-select' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($freeTextOption ? $inputName : null)]); ?>
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

                            <?php if($freeTextOption): ?>
                                <input type="text" name="<?php echo e($inputName); ?>" value="<?php echo e($freeTextValue); ?>"
                                       placeholder="Précisez…" data-other-input="<?php echo e($inputName); ?>"
                                       <?php echo e($isOther ? '' : 'disabled'); ?>

                                       class="mt-2 block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10 <?php echo e($isOther ? '' : 'hidden'); ?>">
                            <?php endif; ?>
                        </div>
                    <?php elseif($field->isDate()): ?>
                        <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['name' => $inputName,'type' => 'date','label' => $field->label,'required' => $field->is_required,'value' => old($oldKey, $draftValue ?? $field->default_value)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inputName),'type' => 'date','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->label),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->is_required),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old($oldKey, $draftValue ?? $field->default_value))]); ?>
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
                    <?php elseif($field->isNumber()): ?>
                        <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['name' => $inputName,'type' => 'number','label' => $field->label,'required' => $field->is_required,'value' => old($oldKey, $draftValue ?? $field->default_value)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inputName),'type' => 'number','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->label),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->is_required),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old($oldKey, $draftValue ?? $field->default_value))]); ?>
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
                    <?php elseif($field->field_type === 'textarea'): ?>
                        <?php if (isset($component)) { $__componentOriginalcc0154580828f80bdab5d7fe416ed74a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcc0154580828f80bdab5d7fe416ed74a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-textarea','data' => ['name' => $inputName,'label' => $field->label,'required' => $field->is_required,'value' => old($oldKey, $draftValue ?? $field->default_value)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inputName),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->label),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->is_required),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old($oldKey, $draftValue ?? $field->default_value))]); ?>
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
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['name' => $inputName,'type' => $field->field_type,'label' => $field->label,'required' => $field->is_required,'value' => old($oldKey, $draftValue ?? $field->default_value),'placeholder' => $field->placeholder]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inputName),'type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->field_type),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->label),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->is_required),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old($oldKey, $draftValue ?? $field->default_value)),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->placeholder)]); ?>
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
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($pageIndex >= 0): ?> </div> <?php endif; ?>
            </div>

            <?php if($pageIndex > 0): ?>
                <div class="flex items-center justify-between border-t border-brand-border px-6 py-4">
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','id' => 'wizard-prev','variant' => 'secondary','icon' => 'arrow-left','class' => 'invisible']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','id' => 'wizard-prev','variant' => 'secondary','icon' => 'arrow-left','class' => 'invisible']); ?>Précédent <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','id' => 'wizard-next']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','id' => 'wizard-next']); ?>Suivant <?php echo $__env->renderComponent(); ?>
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

        <div id="wizard-final-section" <?php if($pageIndex > 0): ?> style="display:none" <?php endif; ?> class="space-y-4">
            <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <label for="priority" class="mb-1.5 block text-[13px] font-medium text-slate-700">Urgence de cette demande</label>
                <select id="priority" name="priority"
                        class="block w-full max-w-xs rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    <?php $__currentLoopData = \App\Enums\FormPriority::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($priority->value); ?>" <?php if(old('priority', $priority->value) === $priority->value): echo 'selected'; endif; ?>><?php echo e($priority->label()); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="mt-1.5 text-xs text-slate-400">Visible par le validateur traitant cette demande - à toi de juger si celle-ci l'est, indépendamment du formulaire utilisé.</p>
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

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit']); ?>Envoyer la demande <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('workflow.my-requests.select-form')).'','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workflow.my-requests.select-form')).'','variant' => 'secondary']); ?>Annuler <?php echo $__env->renderComponent(); ?>
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
                <p id="autosave-status" class="text-xs text-slate-400"></p>
            </div>
        </div>
    </form>

    <div class="mt-3 max-w-2xl">
        <?php if (isset($component)) { $__componentOriginald5ef95010bab1b9161871d6deae199ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5ef95010bab1b9161871d6deae199ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-form','data' => ['action' => route('workflow.my-requests.destroy', $draft),'method' => 'DELETE','confirm' => 'Supprimer ce brouillon ? Tout ce qui a été saisi (et les pièces jointes déjà ajoutées) sera définitivement perdu.','variant' => 'ghost','icon' => 'trash']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workflow.my-requests.destroy', $draft)),'method' => 'DELETE','confirm' => 'Supprimer ce brouillon ? Tout ce qui a été saisi (et les pièces jointes déjà ajoutées) sera définitivement perdu.','variant' => 'ghost','icon' => 'trash']); ?>Supprimer le brouillon <?php echo $__env->renderComponent(); ?>
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

    <script>
        (function () {
            var form = document.getElementById('request-form');
            var status = document.getElementById('autosave-status');
            if (!form || !status) return;

            // Champs "select" avec option "Autre" (voir
            // FieldOption::FREE_TEXT_VALUE) : bascule l'affichage vers un
            // texte libre dès que cette option est choisie. Les deux
            // partagent volontairement le même `name` - le texte libre,
            // placé après dans le DOM, l'emporte naturellement sur le
            // select à la soumission tant qu'il est actif (et
            // inversement, désactivé, il est tout simplement absent de
            // l'envoi).
            document.querySelectorAll('[data-other-select]').forEach(function (select) {
                var key = select.getAttribute('data-other-select');
                var input = form.querySelector('[data-other-input="' + key + '"]');
                if (!input) return;

                select.addEventListener('change', function () {
                    var isOther = select.value === '__AUTRE__';
                    input.classList.toggle('hidden', !isOther);
                    input.disabled = !isOther;
                    if (isOther) input.focus();
                });
            });

            // ==================================================
            // Pagination par section - un formulaire long (35+
            // champs) est bien plus ergonomique découpé section par
            // section que déroulé d'un bloc. Purement visuel côté
            // navigateur : tous les champs restent dans le DOM et
            // partent ensemble à l'envoi - aucun changement côté
            // sauvegarde automatique, validation serveur ou soumission.
            // ==================================================
            var pages = Array.prototype.slice.call(document.querySelectorAll('.wizard-page'));
            var pageTitles = <?php echo json_encode($pageTitles ?? [], 15, 512) ?>;
            if (pages.length > 1) {
                var current = 0;
                var progress = document.getElementById('wizard-progress');
                var progressLabel = document.getElementById('wizard-progress-label');
                var progressDots = document.getElementById('wizard-progress-dots');
                var prevBtn = document.getElementById('wizard-prev');
                var nextBtn = document.getElementById('wizard-next');
                var finalSection = document.getElementById('wizard-final-section');

                progress.classList.remove('hidden');
                progress.classList.add('flex');
                pages.forEach(function (_, i) {
                    var dot = document.createElement('span');
                    dot.className = 'h-1.5 w-1.5 rounded-full bg-brand-border';
                    dot.dataset.dot = i;
                    progressDots.appendChild(dot);
                });

                function render() {
                    pages.forEach(function (page, i) { page.style.display = i === current ? '' : 'none'; });
                    finalSection.style.display = current === pages.length - 1 ? '' : 'none';
                    prevBtn.classList.toggle('invisible', current === 0);
                    nextBtn.style.display = current === pages.length - 1 ? 'none' : '';
                    progressLabel.textContent = 'Section ' + (current + 1) + ' / ' + pages.length
                        + (pageTitles[current] ? ' — ' + pageTitles[current] : '');
                    progressDots.querySelectorAll('[data-dot]').forEach(function (dot) {
                        var isCurrent = Number(dot.dataset.dot) === current;
                        var isPast = Number(dot.dataset.dot) < current;
                        dot.className = 'h-1.5 w-1.5 rounded-full ' + (isCurrent ? 'bg-brand-blue' : isPast ? 'bg-brand-blue/40' : 'bg-brand-border');
                    });
                }

                function currentPageIsValid() {
                    var inputs = pages[current].querySelectorAll('input, select, textarea');
                    for (var i = 0; i < inputs.length; i++) {
                        if (!inputs[i].disabled && !inputs[i].checkValidity()) {
                            inputs[i].reportValidity();
                            return false;
                        }
                    }
                    return true;
                }

                // Après un envoi refusé par le serveur (ex. un champ
                // obligatoire resté vide), l'assistant rouvrait toujours
                // sur la première section - un champ en erreur situé
                // sur une AUTRE section devenait invisible, alors que le
                // message d'erreur, lui, s'affichait bien en haut de
                // l'écran. On saute donc directement à la première
                // section qui contient une erreur, s'il y en a une.
                var firstErroredPage = -1;
                pages.forEach(function (page, i) {
                    if (firstErroredPage === -1 && page.querySelector('.border-red-300')) {
                        firstErroredPage = i;
                    }
                });
                if (firstErroredPage !== -1) current = firstErroredPage;

                nextBtn.addEventListener('click', function () {
                    if (!currentPageIsValid()) return;
                    current = Math.min(current + 1, pages.length - 1);
                    render();
                    pages[current].scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                prevBtn.addEventListener('click', function () {
                    current = Math.max(current - 1, 0);
                    render();
                    pages[current].scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                // Filet de sécurité : empêche un Entrée dans un champ
                // texte de soumettre le formulaire avant la dernière
                // section (comportement natif du navigateur sinon).
                form.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA' && current !== pages.length - 1) {
                        event.preventDefault();
                        nextBtn.click();
                    }
                });

                render();
            }

            var draftUrl = <?php echo json_encode(route('workflow.my-requests.save-draft', $form), 512) ?>;
            var saving = false;

            function saveDraft() {
                if (saving) return Promise.resolve();
                saving = true;
                status.textContent = 'Enregistrement du brouillon…';

                // Les champs de type "fichier" ne sont jamais inclus dans
                // le brouillon automatique - SaveRequestDraftRequest
                // n'accepte que du texte, et un fichier n'a de sens
                // qu'associé à la Request définitive (voir
                // SubmitRequestRequest::prepareForValidation()). On
                // reconstruit donc le FormData à la main plutôt que
                // d'utiliser `new FormData(form)` tel quel.
                var data = new FormData();
                Array.prototype.forEach.call(form.elements, function (el) {
                    if (!el.name || el.type === 'file' || el.disabled) return;
                    data.append(el.name, el.value);
                });

                return fetch(draftUrl, {
                    method: 'POST',
                    body: data,
                    headers: { 'Accept': 'application/json' },
                })
                    .then(function (response) { return response.ok ? response.json() : Promise.reject(); })
                    .then(function (data) {
                        status.textContent = 'Brouillon enregistré à ' + data.savedAt + '.';
                    })
                    .catch(function () {
                        status.textContent = "Échec de l'enregistrement automatique.";
                    })
                    .finally(function () { saving = false; });
            }

            // Sauvegarde automatique toutes les 20 secondes tant que
            // l'écran de saisie reste ouvert - une demande d'entreprise
            // peut prendre du temps à remplir (pièces jointes à
            // rassembler, chiffres à vérifier...), la perdre en fermant
            // l'onglet par erreur n'est pas acceptable.
            var interval = setInterval(saveDraft, 20000);

            // Arrêtée si l'utilisateur envoie réellement la demande ou
            // quitte la page, pour ne pas continuer à sauvegarder un
            // brouillon qui vient d'être supprimé côté serveur
            // (MyRequestController::store()).
            form.addEventListener('submit', function () { clearInterval(interval); });
            window.addEventListener('beforeunload', function () { clearInterval(interval); });
        })();
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', ['title' => $form->name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projects\to step 12 backup\resources\views/workflow/my-requests/create.blade.php ENDPATH**/ ?>