@extends('layouts.auth', ['title' => 'Inscription'])

@section('content')
    <h1 class="text-lg font-semibold mb-1">Créer un compte</h1>
    <p class="text-sm text-gray-500 mb-6">Réservé aux adresses professionnelles Saint-Gobain.</p>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail professionnelle</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   placeholder="prenom.nom@saint-gobain.com"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone (optionnel)</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="entity_id" class="block text-sm font-medium text-gray-700">Entité</label>
                <select id="entity_id" name="entity_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">—</option>
                    @foreach ($entities as $entity)
                        <option value="{{ $entity->id }}" @selected(old('entity_id') == $entity->id)>{{ $entity->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700">Département</label>
                <select id="department_id" name="department_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">—</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="business_function_id" class="block text-sm font-medium text-gray-700">Fonction</label>
                <select id="business_function_id" name="business_function_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">—</option>
                    @foreach ($businessFunctions as $businessFunction)
                        <option value="{{ $businessFunction->id }}" @selected(old('business_function_id') == $businessFunction->id)>{{ $businessFunction->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="application_role_id" class="block text-sm font-medium text-gray-700">Profil</label>
                <select id="application_role_id" name="application_role_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">—</option>
                    @foreach ($applicationRoles as $role)
                        <option value="{{ $role->id }}" @selected(old('application_role_id') == $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="manager_id" class="block text-sm font-medium text-gray-700">Responsable hiérarchique (N+1)</label>
            <select id="manager_id" name="manager_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">Aucun (sommet de la hiérarchie)</option>
                @foreach ($managers as $manager)
                    <option value="{{ $manager->id }}" @selected(old('manager_id') == $manager->id)>{{ $manager->full_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input id="password" type="password" name="password" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmation</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
        </div>

        <button type="submit"
                class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Créer mon compte
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:underline">Se connecter</a>
    </p>
@endsection
