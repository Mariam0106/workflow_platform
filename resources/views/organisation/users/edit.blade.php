@extends('layouts.admin', ['title' => 'Modifier utilisateur'])

@section('content')
    <h1 class="mb-6 text-lg font-semibold">Modifier « {{ $user->full_name }} »</h1>

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

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-brand-blue px-4 py-2 text-sm font-semibold text-white hover:bg-brand-blue-dark">
                Enregistrer
            </button>
        </div>
    </form>

    @can(($user->is_active ? 'deactivate' : 'activate'), $user)
        <form method="POST" action="{{ route($user->is_active ? 'organisation.users.deactivate' : 'organisation.users.activate', $user) }}" class="mt-4 max-w-xl">
            @csrf
            <button type="submit" class="text-sm font-medium {{ $user->is_active ? 'text-brand-danger' : 'text-brand-success' }} hover:underline">
                {{ $user->is_active ? 'Désactiver ce compte' : 'Réactiver ce compte' }}
            </button>
        </form>
    @endcan
@endsection
