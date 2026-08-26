@extends('layouts.admin', ['title' => "Examiner l'inscription"])

@section('content')
    <x-page-header :title="$registration->full_name" :description="$registration->email">
        <x-slot:actions>
            <x-button href="{{ route('organisation.registrations.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Approuver la demande</h2>

            <form method="POST" action="{{ route('organisation.registrations.approve', $registration) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-select name="entity_id" label="Entité" required :options="$entities" :value="old('entity_id', $registration->entity_id)" />
                    <x-form-select name="department_id" label="Département" required :options="$departments" :value="old('department_id', $registration->department_id)" />
                    <x-form-select name="business_function_id" label="Fonction Métier" required :options="$businessFunctions" :value="old('business_function_id', $registration->business_function_id)" />
                </div>

                <div>
                    <label class="mb-1.5 block text-[13px] font-medium text-slate-700">
                        Rôle(s) applicatif(s) <span class="text-brand-danger">*</span>
                    </label>
                    <div class="flex flex-wrap gap-x-6 gap-y-2 rounded-lg border border-brand-border bg-white px-3.5 py-3">
                        @foreach ($applicationRoles as $role)
                            <label class="flex items-center gap-2 text-sm text-brand-navy">
                                <input type="checkbox" name="application_role_ids[]" value="{{ $role->id }}"
                                       @checked(collect(old('application_role_ids', [$applicationRoles->firstWhere('code', 'USER')?->id]))->contains($role->id))
                                       class="rounded border-brand-border text-brand-blue focus:ring-brand-blue/30">
                                {{ $role->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('application_role_ids') <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                </div>

                <x-form-select name="default_application_role_id" label="Rôle par défaut" required :options="$applicationRoles"
                                :value="old('default_application_role_id', $applicationRoles->firstWhere('code', 'USER')?->id)" hint="Doit faire partie des rôles cochés ci-dessus - c'est le rôle proposé à la première connexion." />

                <x-button type="submit" icon="check">Approuver et activer le compte</x-button>
            </form>
        </x-card>

        <div class="space-y-4">
            <x-card>
                <h2 class="mb-3 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Demande initiale</h2>
                <dl class="space-y-2.5 text-sm">
                    <div>
                        <dt class="text-xs text-slate-400">Téléphone</dt>
                        <dd class="text-brand-navy">{{ $registration->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Entité demandée</dt>
                        <dd class="text-brand-navy">{{ $registration->entity?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Département demandé</dt>
                        <dd class="text-brand-navy">{{ $registration->department?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Fonction demandée</dt>
                        <dd class="text-brand-navy">{{ $registration->businessFunction?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Responsable (N+1) indiqué</dt>
                        <dd class="text-brand-navy">{{ $registration->manager?->full_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Envoyée le</dt>
                        <dd class="text-brand-navy">{{ $registration->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card>
                <h2 class="mb-3 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Refuser la demande</h2>
                <form method="POST" action="{{ route('organisation.registrations.reject', $registration) }}"
                      onsubmit="return confirm('Refuser définitivement l\'inscription de « {{ $registration->full_name }} » ?');">
                    @csrf
                    <x-form-textarea name="reason" label="Motif" hint="Optionnel - transmis à la personne par e-mail." />
                    <div class="mt-3">
                        <x-button type="submit" variant="danger" icon="close">Refuser</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
@endsection
