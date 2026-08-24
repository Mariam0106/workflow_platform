@extends('layouts.admin', ['title' => 'Historique'])

@section('content')
    <x-page-header title="Historique / Journal d'audit" description="{{ $auditLogs->total() }} entrée(s) — lecture seule, jamais modifiable." />

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="w-56">
            <x-form-input name="q" :value="$search" placeholder="Rechercher par utilisateur…" />
        </div>
        <div class="w-44">
            <x-form-select name="action" :options="$actions->map(fn ($a) => ['id' => $a, 'name' => $a])" placeholder="Toutes les actions" :value="request('action')" />
        </div>
        <div class="w-44">
            <x-form-select name="entity_type" :options="$entityTypes->map(fn ($e) => ['id' => $e, 'name' => $e])" placeholder="Toutes les entités" :value="request('entity_type')" />
        </div>
        <div class="w-40">
            <x-form-input name="date_from" type="date" label="Du" :value="$dateFrom" />
        </div>
        <div class="w-40">
            <x-form-input name="date_to" type="date" label="Au" :value="$dateTo" />
        </div>
        <x-button type="submit" variant="secondary" size="sm" icon="search">Filtrer</x-button>
        @if ($search || request('action') || request('entity_type') || $dateFrom || $dateTo)
            <x-button href="{{ route('workflow.admin.audit-logs.index') }}" variant="ghost" size="sm" icon="close">Réinitialiser</x-button>
        @endif
    </form>

    <x-card :padded="false">
        @if ($auditLogs->isEmpty())
            <x-empty-state icon="clock" title="Aucune entrée" description="Aucune action ne correspond à ces filtres." />
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Utilisateur</th>
                        <th class="px-5 py-3">Action</th>
                        <th class="px-5 py-3">Entité</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($auditLogs as $log)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3.5 text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-brand-navy">{{ $log->user?->full_name ?? 'Système' }}</td>
                            <td class="px-5 py-3.5"><x-badge :tone="$log->actionTone()">{{ $log->actionLabel() }}</x-badge></td>
                            <td class="px-5 py-3.5 text-slate-600">
                                @if ($log->entityUrl())
                                    <a href="{{ $log->entityUrl() }}" class="text-brand-blue hover:text-brand-blue-dark hover:underline">{{ $log->entityReference() }}</a>
                                @else
                                    {{ $log->entityReference() }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <x-simple-paginator :paginator="$auditLogs" />
        @endif
    </x-card>
@endsection
