<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Prénom</label>
        <input type="text" name="first_name" value="{{ old('first_name', $user?->first_name) }}" required
               class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
    </div>
    <div>
        <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Nom</label>
        <input type="text" name="last_name" value="{{ old('last_name', $user?->last_name) }}" required
               class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
    </div>
</div>

@unless ($user)
    <div>
        <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Email professionnel</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
    </div>
@else
    <p class="text-sm text-slate-500">{{ $user->email }} <span class="text-xs text-slate-400">(email non modifiable ici)</span></p>
@endunless

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Entité</label>
        <select name="entity_id" required class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">
            @foreach ($entities as $entity)
                <option value="{{ $entity->id }}" @selected(old('entity_id', $user?->entity_id) == $entity->id)>{{ $entity->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Département</label>
        <select name="department_id" required class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id', $user?->department_id) == $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Fonction</label>
        <select name="business_function_id" required class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">
            @foreach ($businessFunctions as $businessFunction)
                <option value="{{ $businessFunction->id }}" @selected(old('business_function_id', $user?->business_function_id) == $businessFunction->id)>{{ $businessFunction->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Profil</label>
        <select name="application_role_id" required class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">
            @foreach ($applicationRoles as $role)
                <option value="{{ $role->id }}" @selected(old('application_role_id', $user?->application_role_id) == $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Responsable N+1</label>
    <select name="manager_id" class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">
        <option value="">Aucun</option>
        @foreach ($managers as $manager)
            @continue($user && $manager->id === $user->id)
            <option value="{{ $manager->id }}" @selected(old('manager_id', $user?->manager_id) == $manager->id)>{{ $manager->full_name }}</option>
        @endforeach
    </select>
</div>

@unless ($user)
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Mot de passe</label>
            <input type="password" name="password" required class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Confirmation</label>
            <input type="password" name="password_confirmation" required class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">
        </div>
    </div>
@endunless
