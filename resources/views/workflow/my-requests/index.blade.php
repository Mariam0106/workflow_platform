@extends('layouts.admin', ['title' => 'Mes demandes'])

@section('content')
    @php
        $statusLabels = ['Submitted' => 'en attente', 'Completed' => 'validées', 'Rejected' => 'refusées', 'Draft' => 'brouillons'];
    @endphp

    <x-page-header title="Mes demandes" :description="$requests->total() . ' demande(s)' . ($activeStatus ? ' — ' . ($statusLabels[$activeStatus] ?? $activeStatus) : '')">
        <x-slot:actions>
            @if ($activeStatus)
                <x-button href="{{ route('workflow.my-requests.index') }}" variant="secondary" size="sm">Voir toutes mes demandes</x-button>
            @endif
            <x-button href="{{ route('workflow.my-requests.select-form') }}" icon="plus">Nouvelle demande</x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Le filtre par statut (KPI du dashboard) reste actif pendant la
         recherche - un champ caché le reporte plutôt que d'utiliser
         x-search-input seul, qui ne connaît que "q". --}}
    <form method="GET" class="relative mb-4 w-full max-w-xs">
        @if ($activeStatus)
            <input type="hidden" name="status" value="{{ $activeStatus }}">
        @endif
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
        </span>
        <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher par référence, formulaire…" autocomplete="off"
               class="h-9 w-full rounded-lg border border-brand-border bg-white pl-9 pr-3 text-[13px] text-brand-navy shadow-sm transition placeholder:text-slate-400 hover:border-brand-blue/40 focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
    </form>

    <x-card :padded="false">
        @if ($requests->isEmpty())
            <x-empty-state icon="inbox" title="Aucune demande pour l'instant" description="Créez votre première demande à partir d'un formulaire disponible.">
                <x-slot:actions>
                    <x-button href="{{ route('workflow.my-requests.select-form') }}" icon="plus">Nouvelle demande</x-button>
                </x-slot:actions>
            </x-empty-state>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Référence</th>
                        <th class="px-5 py-3">Formulaire</th>
                        <th class="px-5 py-3">Étape actuelle</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3">Soumise le</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($requests as $requestItem)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                @if ($requestItem->status->value === 'Draft')
                                    <span class="font-medium text-brand-navy">{{ $requestItem->reference_number }}</span>
                                @else
                                    <a href="{{ route('workflow.my-requests.show', $requestItem) }}" class="font-medium text-brand-navy hover:text-brand-blue">
                                        {{ $requestItem->reference_number }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $requestItem->form?->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $requestItem->status->value === 'Draft' ? '—' : ($requestItem->currentStep?->name ?? '—') }}</td>
                            <td class="px-5 py-3.5"><x-request-status-badge :status="$requestItem->status" /></td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $requestItem->status->value === 'Draft' ? 'Modifié le ' . $requestItem->updated_at->format('d/m/Y H:i') : $requestItem->submitted_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                @if ($requestItem->status->value === 'Draft' && $requestItem->form)
                                    <x-button href="{{ route('workflow.my-requests.create', $requestItem->form) }}" size="sm" variant="secondary">Continuer</x-button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <x-simple-paginator :paginator="$requests" />
    </x-card>
@endsection
