<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de bord — Workflow Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <nav class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
        <span class="font-semibold">Workflow Platform</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">Se déconnecter</button>
        </form>
    </nav>

    <main class="max-w-3xl mx-auto px-6 py-10">
        <h1 class="text-xl font-semibold mb-2">Bienvenue, {{ $user->full_name }} 👋</h1>
        <p class="text-sm text-gray-600">
            {{ $user->applicationRole?->name }} — {{ $user->department?->name }} — {{ $user->entity?->name }}
        </p>
        <p class="mt-6 text-sm text-gray-400">
            Placeholder — le vrai tableau de bord (KPI, activités, notifications) arrive à l'Étape 14.
        </p>
    </main>
</body>
</html>
