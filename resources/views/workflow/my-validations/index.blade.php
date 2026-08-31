@extends('layouts.admin', ['title' => 'Mes validations'])

@section('content')
    <x-page-header title="Mes validations" description="{{ $pendingRequests->count() }} demande(s) en attente de votre décision">
        <x-slot:actions>
            <x-button href="{{ route('workflow.my-validations.history') }}" variant="secondary" icon="check">Historique de mes décisions</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4">
        <x-search-input :value="$search" placeholder="Rechercher par référence, formulaire, demandeur…" />
    </div>

    <x-card :padded="false">
        @if ($pendingRequests->isEmpty())
            <x-empty-state icon="check" title="Aucune demande en attente" description="Vous n'avez aucune décision à prendre pour le moment." />
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Référence</th>
                        <th class="px-5 py-3">Urgence</th>
                        <th class="px-5 py-3">Formulaire</th>
                        <th class="px-5 py-3">Demandeur</th>
                        <th class="px-5 py-3">Étape</th>
                        <th class="px-5 py-3">Soumise le</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($pendingRequests as $requestItem)
                        <tr class="transition hover:bg-slate-50/60 {{ $requestItem->priority?->value === 'Urgent' ? 'bg-red-50/40' : '' }}">
                            <td class="px-5 py-3.5 font-medium text-brand-navy">{{ $requestItem->reference_number }}</td>
                            <td class="px-5 py-3.5"><x-priority-badge :priority="$requestItem->priority" /></td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $requestItem->form?->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $requestItem->requester?->full_name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $requestItem->currentStep?->name }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $requestItem->submitted_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <x-button href="{{ route('workflow.my-validations.show', $requestItem) }}" size="sm">Examiner</x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-card>
@endsection
