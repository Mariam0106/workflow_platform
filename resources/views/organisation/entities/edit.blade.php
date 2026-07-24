@extends('layouts.admin', ['title' => 'Modifier entité'])

@section('content')
    <x-page-header title="{{ $entity->name }}" description="Modifier l'entité." />

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3.5 text-sm text-red-700">
            <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('organisation.entities.update', $entity) }}" class="max-w-lg space-y-4 rounded-xl border border-brand-border bg-white p-6">
        @csrf
        @method('PUT')
        @include('organisation.entities._form', ['entity' => $entity])
        <x-button type="submit">Enregistrer</x-button>
    </form>
@endsection
