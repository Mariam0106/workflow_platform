@extends('layouts.admin', ['title' => 'Tableau de bord'])

@section('content')
    <x-page-header title="Bonjour {{ $user->first_name }}" description="{{ $user->applicationRole?->name }} — {{ $user->department?->name }} — {{ $user->entity?->name }}" />

    {{-- ==================================================
         KPI
    =================================================== --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <x-kpi-card label="Formulaires soumis" :value="$kpis['submitted']" icon="inbox" accent="blue" />
        <x-kpi-card label="En attente" :value="$kpis['pending']" icon="clock" accent="warning" />
        <x-kpi-card label="À valider" :value="$kpis['to_validate']" icon="check" accent="warning" />
        <x-kpi-card label="Validées" :value="$kpis['approved']" icon="check" accent="success" />
        <x-kpi-card label="Refusées" :value="$kpis['rejected']" icon="bell" accent="danger" />
    </div>

    {{-- ==================================================
         ACTIONS + ACTIVITÉ RÉCENTE
    =================================================== --}}
    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Mes actions --}}
        <div class="rounded-xl border border-brand-border bg-white p-5 lg:col-span-1">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Mes actions</h2>
            <ul class="space-y-1">
                @php
                    $actions = [
                        ['label' => 'Nouvelle demande', 'icon' => 'file'],
                        ['label' => 'Mes demandes', 'icon' => 'inbox'],
                        ['label' => 'Historique', 'icon' => 'clock'],
                        ['label' => 'Notifications', 'icon' => 'bell'],
                    ];
                @endphp
                @foreach ($actions as $action)
                    <li class="flex items-center justify-between rounded-lg px-2.5 py-2 text-[13px] text-slate-300">
                        <span class="flex items-center gap-2.5">
                            @include('layouts.partials.icon', ['name' => $action['icon'], 'class' => 'h-[18px] w-[18px]'])
                            {{ $action['label'] }}
                        </span>
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-400">Bientôt</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Activité récente --}}
        <div class="rounded-xl border border-brand-border bg-white p-5 lg:col-span-2">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Activité récente</h2>

            @forelse ($recentNotifications as $notification)
                <div class="flex items-start gap-3 border-b border-brand-border py-3 last:border-0 last:pb-0">
                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-blue/10 text-brand-blue">
                        @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-3.5 w-3.5'])
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-brand-navy">{{ $notification->title }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $notification->message }}</p>
                    </div>
                    <span class="shrink-0 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="py-8 text-center">
                    <p class="text-sm text-slate-400">Aucune activité pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
