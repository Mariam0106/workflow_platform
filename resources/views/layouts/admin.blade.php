<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Administration' }} — Workflow Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-brand-bg text-brand-navy antialiased">
    <nav class="flex items-center justify-between border-b border-brand-border bg-white px-6 py-3.5">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/saint-gobain-logo.jpg') }}" alt="Saint-Gobain" class="h-7 w-auto">
            <span class="text-sm font-semibold text-slate-400">/ Administration</span>
        </div>
        <div class="flex items-center gap-4 text-sm text-slate-500">
            <a href="{{ route('dashboard') }}" class="hover:text-brand-blue">Tableau de bord</a>
            <span>{{ request()->user()?->full_name }}</span>
        </div>
    </nav>

    <main class="mx-auto max-w-5xl px-6 py-8">
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
