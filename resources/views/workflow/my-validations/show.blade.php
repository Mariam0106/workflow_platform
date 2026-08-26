@extends('layouts.admin', ['title' => (string) $requestModel->reference_number])

@section('content')
    <x-page-header :title="$requestModel->reference_number" :description="$requestModel->form?->name . ' — soumise par ' . $requestModel->requester?->full_name">
        <x-slot:actions>
            <x-button href="{{ route('workflow.my-validations.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 flex items-center gap-2">
        <x-request-status-badge :status="$requestModel->status" />
        <x-priority-badge :priority="$requestModel->priority" />
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Informations soumises</h2>
            <dl class="grid grid-cols-2 gap-x-4 gap-y-3.5 text-sm">
                @php $previousSection = null; @endphp
                @foreach ($requestModel->requestValues as $value)
                    @if ($value->formField?->section_title && $value->formField->section_title !== $previousSection)
                        <p class="col-span-2 {{ $previousSection ? 'mt-2 border-t border-brand-border pt-3' : '' }} text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                            {{ $value->formField->section_title }}
                        </p>
                    @endif
                    @php $previousSection = $value->formField?->section_title ?? $previousSection; @endphp
                    <div>
                        <dt class="text-xs text-slate-400">{{ $value->formField?->label }}</dt>
                        <dd class="mt-0.5 text-brand-navy">
                            @if ($value->formField?->isMontant() && is_numeric($value->value))
                                {{ number_format((float) $value->value, 2, ',', ' ') }} MAD
                            @else
                                {{ $value->value }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>

            @if ($requestModel->attachments->isNotEmpty())
                <h2 class="mb-3 mt-6 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Pièces jointes ({{ $requestModel->attachments->count() }})</h2>
                <div class="rounded-lg border border-brand-border">
                    <x-attachment-list :attachments="$requestModel->attachments" />
                </div>
            @endif

            @if ($requestModel->validations->isNotEmpty())
                <h2 class="mb-3 mt-6 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Historique</h2>
                <ul class="space-y-3">
                    @foreach ($requestModel->validations as $validation)
                        <li class="text-sm">
                            <span class="font-medium text-brand-navy">{{ $validation->validator?->full_name }}</span> —
                            {{ $validation->decision->value === 'Approved' ? 'Approuvé' : 'Rejeté' }}
                            @if ($validation->comment)
                                <span class="text-slate-500">« {{ $validation->comment }} »</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Votre décision</h2>

            @if ($canDecide)
                <form method="POST" action="{{ route('workflow.my-validations.decide', $requestModel) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Décision <span class="text-brand-danger">*</span></label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2.5 rounded-lg border border-brand-border px-3.5 py-2.5 text-sm text-brand-navy transition hover:border-brand-blue/40">
                                <input type="radio" name="decision" value="Approved" required class="text-brand-blue focus:ring-brand-blue/30">
                                Approuver
                            </label>
                            <label class="flex items-center gap-2.5 rounded-lg border border-brand-border px-3.5 py-2.5 text-sm text-brand-navy transition hover:border-brand-blue/40">
                                <input type="radio" name="decision" value="Rejected" required class="text-brand-blue focus:ring-brand-blue/30">
                                Rejeter
                            </label>
                        </div>
                        @error('decision') <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                    </div>

                    <x-form-textarea name="comment" label="Commentaire" hint="Obligatoire en cas de rejet." />

                    <x-button type="submit">Enregistrer la décision</x-button>
                </form>
            @else
                <p class="text-sm text-slate-500">Cette demande n'est plus (ou pas encore) en attente de votre décision.</p>
            @endif
        </x-card>
    </div>
@endsection
