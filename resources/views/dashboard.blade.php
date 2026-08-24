@extends('layouts.admin', ['title' => 'Tableau de bord'])

@section('content')
    <div class="mb-6">
        <h1 class="text-lg font-semibold text-brand-navy">Dashboard</h1>
        <p class="mt-0.5 text-[13px] text-slate-400">{{ $user->activeApplicationRole()?->name }} · {{ $user->department?->name }} · {{ $user->entity?->name }}</p>
    </div>

    {{-- ==================================================
         KPI - propres au Rôle actif (BR-06), voir DashboardController
    =================================================== --}}
    @php
        // Classes Tailwind toujours écrites en toutes lettres (jamais
        // interpolées) - le scanner de contenu de Tailwind (build Vite)
        // repère les noms de classes en cherchant des sous-chaînes
        // littérales dans le fichier source ; "sm:grid-cols-{{ $n }}"
        // ne matcherait rien du tout au build.
        $gridColsClass = match (count($cards)) {
            3 => 'sm:grid-cols-3',
            6 => 'sm:grid-cols-3',
            default => 'sm:grid-cols-4',
        };
    @endphp
    <div class="grid grid-cols-2 gap-4 {{ $gridColsClass }}">
        @foreach ($cards as $card)
            <x-kpi-card
                :label="$card['label']"
                :value="$card['value']"
                :icon="$card['icon']"
                :accent="$card['accent']"
                :href="$card['href'] ?? null"
            />
        @endforeach
    </div>

    {{-- ==================================================
         ACTIONS + ACTIVITÉ RÉCENTE
    =================================================== --}}
    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Mes actions - propres au Rôle actif, aucune redondance entre profils --}}
        <x-card class="lg:col-span-1">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Mes actions</h2>
            <ul class="space-y-1">
                @php
                    $actions = match ($activeRole) {
                        \App\Enums\ApplicationRoleCode::Administrator => [
                            ['label' => 'Ajouter un utilisateur', 'icon' => 'users', 'href' => route('organisation.users.create')],
                            ['label' => 'Ajouter un département', 'icon' => 'building', 'href' => route('organisation.departments.create')],
                            ['label' => 'Ajouter une entité', 'icon' => 'layers', 'href' => route('organisation.entities.create')],
                        ],
                        \App\Enums\ApplicationRoleCode::Validator => [
                            ['label' => 'Mes validations', 'icon' => 'check', 'href' => route('workflow.my-validations.index')],
                        ],
                        default => [
                            ['label' => 'Nouvelle demande', 'icon' => 'plus', 'href' => route('workflow.my-requests.select-form')],
                            ['label' => 'Mes demandes', 'icon' => 'inbox', 'href' => route('workflow.my-requests.index')],
                        ],
                    };
                @endphp
                @foreach ($actions as $action)
                    <li>
                        <a href="{{ $action['href'] }}" class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[13px] font-medium text-slate-600 transition hover:bg-slate-50 hover:text-brand-navy">
                            @include('layouts.partials.icon', ['name' => $action['icon'], 'class' => 'h-[18px] w-[18px] text-slate-400'])
                            {{ $action['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </x-card>

        {{-- Activité récente --}}
        <x-card class="lg:col-span-2">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Activité récente</h2>

            @forelse ($recentNotifications as $notification)
                @php
                    $notificationHref = null;
                    if ($notification->request) {
                        $notificationHref = $notification->request->requester_id === $user->id
                            ? route('workflow.my-requests.show', $notification->request)
                            : route('workflow.my-validations.show', $notification->request);
                    }
                @endphp
                <{{ $notificationHref ? 'a' : 'div' }}
                    @if ($notificationHref) href="{{ $notificationHref }}" @endif
                    class="group flex items-start gap-3 border-b border-brand-border py-3 last:border-0 last:pb-0 {{ $notificationHref ? 'transition hover:bg-slate-50/60 -mx-2 px-2 rounded-lg' : '' }}"
                >
                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-blue/10 text-brand-blue">
                        @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-3.5 w-3.5'])
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-brand-navy {{ $notificationHref ? 'group-hover:text-brand-blue' : '' }}">{{ $notification->title }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $notification->message }}</p>
                    </div>
                    <span class="shrink-0 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                </{{ $notificationHref ? 'a' : 'div' }}>
            @empty
                <x-empty-state icon="bell" title="Aucune activité pour le moment" />
            @endforelse
        </x-card>
    </div>
@endsection
