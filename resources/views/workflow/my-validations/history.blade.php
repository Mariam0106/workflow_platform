@extends('layouts.admin', ['title' => 'Mes décisions'])

@section('content')
    @php
        $decisionLabels = ['Approved' => 'approuvées', 'Rejected' => 'rejetées'];
    @endphp

    <x-page-header title="Mes décisions" :description="$validations->total() . ' décision(s)' . ($activeDecision ? ' — ' . ($decisionLabels[$activeDecision] ?? $activeDecision) : '')">
        <x-slot:actions>
            @if ($activeDecision)
                <x-button href="{{ route('workflow.my-validations.history') }}" variant="secondary" size="sm">Voir toutes mes décisions</x-button>
            @endif
            <x-button href="{{ route('workflow.my-validations.index') }}" variant="secondary" icon="arrow-left">File d'attente</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="relative mb-4 w-full max-w-xs">
        @if ($activeDecision)
            <input type="hidden" name="decision" value="{{ $activeDecision }}">
        @endif
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
        </span>
        <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher par référence, formulaire…" autocomplete="off"
               class="h-9 w-full rounded-lg border border-brand-border bg-white pl-9 pr-3 text-[13px] text-brand-navy shadow-sm transition placeholder:text-slate-400 hover:border-brand-blue/40 focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
    </form>

    <x-card :padded="false">
        @if ($validations->isEmpty())
            <x-empty-state icon="check" title="Aucune décision pour l'instant" />
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Référence</th>
                        <th class="px-5 py-3">Formulaire</th>
                        <th class="px-5 py-3">Décision</th>
                        <th class="px-5 py-3">Commentaire</th>
                        <th class="px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($validations as $validation)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('workflow.my-validations.show', $validation->request) }}" class="font-medium text-brand-navy hover:text-brand-blue">
                                    {{ $validation->request?->reference_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $validation->request?->form?->name }}</td>
                            <td class="px-5 py-3.5">
                                <x-badge :tone="$validation->decision->value === 'Approved' ? 'success' : 'danger'">
                                    {{ $validation->decision->value === 'Approved' ? 'Approuvée' : 'Rejetée' }}
                                </x-badge>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $validation->comment ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $validation->validated_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <x-simple-paginator :paginator="$validations" />
    </x-card>
@endsection
