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
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Circuit de validation</h2>
                <p class="mt-0.5 text-xs text-slate-400">Les étapes à venir sont une prévision basée sur votre demande - elles peuvent changer si un Administrateur modifie le circuit.</p>
            </div>
            <div class="divide-y divide-brand-border">
                @foreach ($validationPath as $item)
                    @php
                        $statusStyles = [
                            'validee' => ['dot' => 'bg-brand-success', 'text' => 'text-brand-success', 'label' => 'Validée'],
                            'rejetee' => ['dot' => 'bg-brand-danger', 'text' => 'text-brand-danger', 'label' => 'Rejetée'],
                            'en_cours' => ['dot' => 'bg-brand-warning', 'text' => 'text-brand-warning', 'label' => 'En cours'],
                            'a_venir' => ['dot' => 'bg-slate-300', 'text' => 'text-slate-400', 'label' => 'À venir'],
                        ][$item['status']];
                    @endphp
                    <div class="flex items-center justify-between gap-4 px-5 py-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $statusStyles['dot'] }}"></span>
                            <div class="min-w-0">
                                <p class="truncate text-[13px] font-medium text-brand-navy">{{ $item['step']->name }}</p>
                                <p class="truncate text-xs text-slate-400">{{ $item['validator_label'] }}</p>
                                @if ($item['comment'])
                                    <p class="mt-0.5 text-[13px] text-slate-600">{{ $item['comment'] }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="text-[13px] font-medium {{ $statusStyles['text'] }}">{{ $statusStyles['label'] }}</span>
                            @if ($item['decided_at'])
                                <p class="text-xs text-slate-400">{{ $item['decided_at']->format('d/m/Y à H:i') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
@endsection
