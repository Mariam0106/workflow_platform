<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Workflow Platform' }} — Saint-Gobain Maroc</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-brand-bg text-brand-navy antialiased">
    <div class="flex min-h-screen">

        {{-- ==================================================
             SIDEBAR
        =================================================== --}}
        <aside class="flex w-60 shrink-0 flex-col border-r border-brand-border bg-white">
            <div class="flex items-center gap-2 border-b border-brand-border px-5 py-4">
                <img src="{{ asset('images/saint-gobain-logo.jpg') }}" alt="Saint-Gobain" class="h-6 w-auto">
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
                {{-- Général --}}
                <div>
                    <p class="mb-1.5 px-2.5 text-[11px] font-semibold uppercase tracking-wide text-slate-400">Général</p>
                    <ul class="space-y-0.5">
                        @include('layouts.partials.nav-item', ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'])
                        @include('layouts.partials.nav-item', ['label' => 'Demandes', 'icon' => 'inbox', 'disabled' => true])
                        @include('layouts.partials.nav-item', ['label' => 'Mes validations', 'icon' => 'check', 'disabled' => true])
                        @include('layouts.partials.nav-item', ['label' => 'Notifications', 'icon' => 'bell', 'disabled' => true])
                        @include('layouts.partials.nav-item', ['label' => 'Historique', 'icon' => 'clock', 'disabled' => true])
                    </ul>
                </div>

                {{-- Administration (Admin uniquement) --}}
                @can('viewAny', \App\Models\User::class)
                    <div>
                        <p class="mb-1.5 px-2.5 text-[11px] font-semibold uppercase tracking-wide text-slate-400">Administration</p>
                        <ul class="space-y-0.5">
                            @include('layouts.partials.nav-item', ['label' => 'Utilisateurs', 'route' => 'organisation.users.index', 'routePattern' => 'organisation.users.*', 'icon' => 'users'])
                            @include('layouts.partials.nav-item', ['label' => 'Départements', 'route' => 'organisation.departments.index', 'routePattern' => 'organisation.departments.*', 'icon' => 'building'])
                            @include('layouts.partials.nav-item', ['label' => 'Entités', 'route' => 'organisation.entities.index', 'routePattern' => 'organisation.entities.*', 'icon' => 'layers'])
                            @include('layouts.partials.nav-item', ['label' => 'Business Functions', 'icon' => 'briefcase', 'disabled' => true])
                            @include('layouts.partials.nav-item', ['label' => 'Formulaires', 'icon' => 'file', 'disabled' => true])
                            @include('layouts.partials.nav-item', ['label' => 'Workflow', 'icon' => 'branch', 'disabled' => true])
                            @include('layouts.partials.nav-item', ['label' => 'Rapports', 'icon' => 'chart', 'disabled' => true])
                            @include('layouts.partials.nav-item', ['label' => 'Paramètres', 'icon' => 'settings', 'disabled' => true])
                        </ul>
                    </div>
                @endcan
            </nav>

            {{-- Compte --}}
            <div class="border-t border-brand-border p-3">
                <div class="flex items-center gap-2.5 rounded-lg px-2.5 py-2">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-blue text-xs font-semibold text-white">
                        {{ mb_substr(auth()->user()->first_name ?? '', 0, 1) }}{{ mb_substr(auth()->user()->last_name ?? '', 0, 1) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-medium text-brand-navy">{{ auth()->user()->full_name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ auth()->user()->applicationRole?->name }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-2.5 py-1.5 text-left text-[13px] text-slate-500 hover:bg-slate-50 hover:text-brand-danger">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </aside>

        {{-- ==================================================
             CONTENU
        =================================================== --}}
        <div class="flex-1">
            <main class="mx-auto max-w-5xl px-8 py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
