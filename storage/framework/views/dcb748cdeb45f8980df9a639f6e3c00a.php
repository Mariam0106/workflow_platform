<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'value' => null, 'users', 'entities', 'departments', 'label' => 'Utilisateur', 'hint' => null, 'syncBusinessFunctionField' => null]));

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

foreach (array_filter((['name', 'value' => null, 'users', 'entities', 'departments', 'label' => 'Utilisateur', 'hint' => null, 'syncBusinessFunctionField' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $uid = 'up-' . \Illuminate\Support\Str::random(8);
    $selectedUser = $value ? $users->firstWhere('id', (int) $value) : null;

    $usersPayload = $users->map(fn ($u) => [
        'id' => $u->id,
        'name' => $u->full_name,
        'function' => $u->businessFunction?->name,
        'business_function_id' => $u->business_function_id,
        'entity_id' => $u->entity_id,
        'department_id' => $u->department_id,
    ])->values();
?>

<div data-uid="<?php echo e($uid); ?>" <?php if($syncBusinessFunctionField): ?> data-sync-business-function="<?php echo e($syncBusinessFunctionField); ?>" <?php endif; ?>>
    <label class="mb-1.5 block text-[13px] font-medium text-slate-700"><?php echo e($label); ?></label>

    <div class="mb-2 grid grid-cols-2 gap-2">
        <select data-role="entity-filter" class="rounded-lg border border-brand-border bg-white px-2.5 py-2 text-[13px] text-brand-navy shadow-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
            <option value="">Toutes les entités</option>
            <?php $__currentLoopData = $entities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($entity->id); ?>"><?php echo e($entity->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select data-role="department-filter" class="rounded-lg border border-brand-border bg-white px-2.5 py-2 text-[13px] text-brand-navy shadow-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
            <option value="">Tous les départements</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($department->id); ?>" data-entity="<?php echo e($department->entity_id); ?>"><?php echo e($department->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="relative">
        <input type="text" data-role="search" autocomplete="off" placeholder="Cliquer pour choisir, ou taper un nom…"
               value="<?php echo e($selectedUser?->full_name); ?>"
               class="block w-full cursor-pointer rounded-lg border border-brand-border bg-white px-3.5 py-2.5 pr-9 text-sm text-brand-navy shadow-sm transition focus:cursor-text focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
        <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400">
            <svg viewBox="0 0 18 18" fill="none" class="h-3.5 w-3.5"><path d="M4.5 7l4.5 4.5L13.5 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <input type="hidden" name="<?php echo e($name); ?>" data-role="value" value="<?php echo e($value); ?>">
        <div data-role="results" class="absolute z-20 mt-1 hidden max-h-64 w-full overflow-y-auto rounded-lg border border-brand-border bg-white py-1 shadow-lg"></div>
    </div>

    <?php if($syncBusinessFunctionField): ?>
        <p data-role="sync-hint" class="mt-1.5 hidden text-xs text-brand-blue"></p>
    <?php endif; ?>

    <?php if($hint): ?>
        <p class="mt-1.5 text-xs text-slate-400"><?php echo e($hint); ?></p>
    <?php endif; ?>

    <script type="application/json" data-role="dataset"><?php echo json_encode($usersPayload, 15, 512) ?></script>

    <script>
        (function () {
            var root = document.querySelector('[data-uid="<?php echo e($uid); ?>"]');
            if (!root || root.dataset.bound) return;
            root.dataset.bound = '1';

            var users = JSON.parse(root.querySelector('[data-role="dataset"]').textContent);
            var searchInput = root.querySelector('[data-role="search"]');
            var valueInput = root.querySelector('[data-role="value"]');
            var resultsPanel = root.querySelector('[data-role="results"]');
            var entityFilter = root.querySelector('[data-role="entity-filter"]');
            var departmentFilter = root.querySelector('[data-role="department-filter"]');
            var syncHint = root.querySelector('[data-role="sync-hint"]');

            // Champ "Fonction Métier validatrice" externe (au-dessus,
            // hors de ce composant) - le relier ici évite à l'Admin de
            // rechercher parmi tout le monde alors qu'il vient déjà de
            // préciser la Fonction Métier voulue juste au-dessus.
            var syncFieldId = root.dataset.syncBusinessFunction;
            var syncField = syncFieldId ? document.getElementById(syncFieldId) : null;

            function syncedBusinessFunctionId() {
                return syncField && syncField.value ? syncField.value : null;
            }

            function updateSyncHint() {
                if (!syncHint) return;
                var bizId = syncedBusinessFunctionId();
                if (bizId) {
                    var label = syncField.options[syncField.selectedIndex].text;
                    syncHint.textContent = 'Filtré sur la Fonction Métier « ' + label + ' » sélectionnée ci-dessus.';
                    syncHint.classList.remove('hidden');
                } else {
                    syncHint.classList.add('hidden');
                }
            }

            if (syncField) {
                syncField.addEventListener('change', function () {
                    updateSyncHint();
                    render();
                });
                updateSyncHint();
            }

            function currentEntityId() {
                return <?php echo e($selectedUser?->entity_id ?? 'null'); ?>;
            }
            function currentDepartmentId() {
                return <?php echo e($selectedUser?->department_id ?? 'null'); ?>;
            }
            // Pré-sélectionne les filtres sur l'Entité/le Département de
            // l'Utilisateur déjà choisi (édition d'une étape existante),
            // pour ne pas donner l'impression que le filtre l'a exclu.
            if (currentEntityId()) entityFilter.value = String(currentEntityId());
            if (currentDepartmentId()) departmentFilter.value = String(currentDepartmentId());

            function filteredUsers() {
                var q = searchInput.value.trim().toLowerCase();
                var entityId = entityFilter.value;
                var departmentId = departmentFilter.value;
                var bizId = syncedBusinessFunctionId();

                return users.filter(function (u) {
                    if (bizId && String(u.business_function_id) !== bizId) return false;
                    if (entityId && String(u.entity_id) !== entityId) return false;
                    if (departmentId && String(u.department_id) !== departmentId) return false;
                    if (q && u.name.toLowerCase().indexOf(q) !== 0 && u.name.toLowerCase().indexOf(' ' + q) === -1) return false;
                    return true;
                }).slice(0, 50);
            }

            function render() {
                var matches = filteredUsers();

                if (matches.length === 0) {
                    resultsPanel.innerHTML = '<p class="px-3 py-2.5 text-[13px] text-slate-400">Aucun utilisateur ne correspond.</p>';
                } else {
                    resultsPanel.innerHTML = matches.map(function (u) {
                        var fn = u.function ? '<span class="text-slate-400"> — ' + u.function + '</span>' : '';
                        return '<button type="button" data-id="' + u.id + '" data-name="' + u.name.replace(/"/g, '&quot;') + '" ' +
                            'class="block w-full px-3 py-2 text-left text-[13px] text-brand-navy transition hover:bg-brand-blue/[0.06]">' +
                            u.name + fn + '</button>';
                    }).join('');
                }

                resultsPanel.classList.remove('hidden');
            }

            searchInput.addEventListener('focus', render);
            searchInput.addEventListener('click', render);
            searchInput.addEventListener('input', function () {
                valueInput.value = ''; // retape => oblige à re-choisir un résultat réel
                render();
            });
            entityFilter.addEventListener('change', function () {
                // Le Département filtré doit rester cohérent avec l'Entité choisie.
                Array.prototype.forEach.call(departmentFilter.options, function (opt) {
                    if (!opt.value) return;
                    opt.hidden = entityFilter.value !== '' && opt.dataset.entity !== entityFilter.value;
                });
                if (departmentFilter.selectedOptions[0] && departmentFilter.selectedOptions[0].hidden) {
                    departmentFilter.value = '';
                }
                render();
            });
            departmentFilter.addEventListener('change', render);

            resultsPanel.addEventListener('mousedown', function (event) {
                var button = event.target.closest('button[data-id]');
                if (!button) return;
                valueInput.value = button.dataset.id;
                searchInput.value = button.dataset.name;
                resultsPanel.classList.add('hidden');
            });

            document.addEventListener('click', function (event) {
                if (!root.contains(event.target)) {
                    resultsPanel.classList.add('hidden');
                }
            });
        })();
    </script>
</div>
<?php /**PATH C:\projects\to step 12 backup\resources\views/components/user-picker.blade.php ENDPATH**/ ?>