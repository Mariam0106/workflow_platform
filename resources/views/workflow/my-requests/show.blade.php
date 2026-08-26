@extends('layouts.admin', ['title' => (string) $requestModel->reference_number])

@section('content')
    <x-page-header :title="$requestModel->reference_number" :description="$requestModel->form?->name">
        <x-slot:actions>
            <x-button href="{{ route('workflow.my-requests.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 flex items-center gap-2">
        <x-request-status-badge :status="$requestModel->status" />
        <x-priority-badge :priority="$requestModel->priority" />
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-1">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Suivi</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-slate-400">Étape actuelle</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $requestModel->currentStep?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Soumise le</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $requestModel->submitted_at?->format('d/m/Y à H:i') }}</dd>
                </div>
                @if ($requestModel->completed_at)
                    <div>
                        <dt class="text-xs text-slate-400">Clôturée le</dt>
                        <dd class="mt-0.5 text-brand-navy">{{ $requestModel->completed_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </x-card>

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
        </x-card>

        <x-card class="lg:col-span-3" :padded="false">
            <div class="border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Pièces jointes ({{ $requestModel->attachments->count() }})</h2>
            </div>
            @if ($requestModel->attachments->isEmpty())
                <x-empty-state icon="file" title="Aucune pièce jointe" />
            @else
                <x-attachment-list :attachments="$requestModel->attachments" />
            @endif
        </x-card>

        <x-card class="lg:col-span-3" :padded="false">
            <div class="border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Historique des validations</h2>
            </div>

            @if ($requestModel->validations->isEmpty())
                <x-empty-state icon="check" title="En attente de la première validation" />
            @else
                <ul class="divide-y divide-brand-border">
                    @foreach ($requestModel->validations as $validation)
                        <li class="flex items-start gap-3 px-5 py-3">
                            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $validation->decision->value === 'Approved' ? 'bg-green-50 text-brand-success' : 'bg-red-50 text-brand-danger' }}">
                                @include('layouts.partials.icon', ['name' => $validation->decision->value === 'Approved' ? 'check' : 'close', 'class' => 'h-3.5 w-3.5'])
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-brand-navy">
                                    {{ $validation->validator?->full_name }} —
                                    {{ $validation->decision->value === 'Approved' ? 'Approuvé' : 'Rejeté' }}
                                </p>
                                @if ($validation->comment)
                                    <p class="mt-0.5 text-[13px] text-slate-600">{{ $validation->comment }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 text-xs text-slate-400">{{ $validation->validated_at?->format('d/m/Y H:i') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>
@endsection
