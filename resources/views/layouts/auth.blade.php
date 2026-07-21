<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Workflow Platform' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">
        <div class="mb-8 text-xl font-semibold text-gray-700">Workflow Platform</div>
        <div class="w-full max-w-md bg-white shadow-sm rounded-lg p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>
