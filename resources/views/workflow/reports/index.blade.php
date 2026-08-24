@extends('layouts.admin', ['title' => 'Rapports'])

@section('content')
    <x-page-header title="Rapports" description="Vue d'ensemble des demandes et de l'activité de la plateforme." />

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-kpi-card label="En cours" :value="$byStatus['Submitted'] ?? 0" icon="clock" accent="warning" />
        <x-kpi-card label="Validées" :value="$byStatus['Completed'] ?? 0" icon="check" accent="success" />
        <x-kpi-card label="Rejetées" :value="$byStatus['Rejected'] ?? 0" icon="close" accent="danger" />
        <x-kpi-card label="Formulaires publiés" :value="$totalForms" icon="file" accent="blue" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-card :padded="false">
            <div class="border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Demandes par formulaire</h2>
            </div>
            @if ($byForm->isEmpty())
                <x-empty-state icon="file" title="Aucune donnée" />
            @else
                <ul class="divide-y divide-brand-border">
                    @foreach ($byForm as $row)
                        <li class="flex items-center justify-between px-5 py-3 text-sm">
                            <span class="text-brand-navy">{{ $row->form_name }}</span>
                            <span class="font-medium text-slate-600">{{ $row->total }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        <x-card :padded="false">
            <div class="border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Validateurs les plus actifs</h2>
            </div>
            @if ($topValidators->isEmpty())
                <x-empty-state icon="users" title="Aucune donnée" />
            @else
                <ul class="divide-y divide-brand-border">
                    @foreach ($topValidators as $validator)
                        <li class="flex items-center justify-between px-5 py-3 text-sm">
                            <span class="text-brand-navy">{{ $validator->full_name }}</span>
                            <span class="font-medium text-slate-600">{{ $validator->validations_count }} décision(s)</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>

    @if ($averageResolutionDays !== null)
        <x-card class="mt-4">
            <p class="text-sm text-slate-600">
                Délai moyen de traitement d'une demande validée :
                <span class="font-semibold text-brand-navy">{{ $averageResolutionDays }} jour(s)</span>
            </p>
        </x-card>
    @endif
@endsection
