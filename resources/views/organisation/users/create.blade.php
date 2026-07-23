@extends('layouts.admin', ['title' => 'Nouvel utilisateur'])

@section('content')
    <h1 class="mb-6 text-lg font-semibold">Nouvel utilisateur</h1>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3.5 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('organisation.users.store') }}" class="max-w-xl space-y-4 rounded-xl border border-brand-border bg-white p-6">
        @csrf
        @include('organisation.users._form', ['user' => null])

        <button type="submit" class="rounded-lg bg-brand-blue px-4 py-2 text-sm font-semibold text-white hover:bg-brand-blue-dark">
            Créer
        </button>
    </form>
@endsection
