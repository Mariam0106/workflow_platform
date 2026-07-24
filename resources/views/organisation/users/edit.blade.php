@extends('layouts.admin', ['title' => 'Modifier utilisateur'])

@section('content')
    <x-page-header title="{{ $user->full_name }}" description="Modifier les informations du compte." />

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3.5 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('organisation.users.update', $user) }}" class="max-w-xl space-y-4 rounded-xl border border-brand-border bg-white p-6">
        @csrf
        @method('PUT')
        @include('organisation.users._form', ['user' => $user])

        <x-button type="submit">Enregistrer</x-button>
    </form>

    @can(($user->is_active ? 'deactivate' : 'activate'), $user)
        <form method="POST" action="{{ route($user->is_active ? 'organisation.users.deactivate' : 'organisation.users.activate', $user) }}" class="mt-4 max-w-xl">
            @csrf
            <x-button type="submit" :variant="$user->is_active ? 'danger-text' : 'success-text'">
                {{ $user->is_active ? 'Désactiver ce compte' : 'Réactiver ce compte' }}
            </x-button>
        </form>
    @endcan
@endsection
