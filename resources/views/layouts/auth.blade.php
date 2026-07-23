<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Workflow Platform' }} — Saint-Gobain Maroc</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-brand-bg text-brand-navy antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12">

        {{-- Logo --}}
        <div class="mb-9 flex flex-col items-center gap-2.5">
            <img src="{{ asset('images/saint-gobain-logo.jpg') }}" alt="Saint-Gobain" class="h-11 w-auto">
            <p class="text-[13px] font-medium text-slate-500">Workflow Platform</p>
        </div>

        {{-- Carte --}}
        <div class="w-full @yield('width', 'max-w-[400px]') rounded-xl border border-brand-border bg-white p-8 shadow-sm">
            @yield('content')
        </div>

        <p class="mt-8 text-xs text-slate-400">© {{ date('Y') }} Saint-Gobain Maroc</p>
    </div>
</body>
</html>
