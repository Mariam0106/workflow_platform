@extends('layouts.admin', ['title' => $user->full_name])

@section('content')
    <h1 class="mb-6 text-lg font-semibold">{{ $user->full_name }}</h1>

    <dl class="max-w-xl divide-y divide-brand-border rounded-xl border border-brand-border bg-white text-sm">
        <div class="flex justify-between px-4 py-3"><dt class="text-slate-500">Email</dt><dd>{{ $user->email }}</dd></div>
        <div class="flex justify-between px-4 py-3"><dt class="text-slate-500">Entité</dt><dd>{{ $user->entity?->name }}</dd></div>
        <div class="flex justify-between px-4 py-3"><dt class="text-slate-500">Département</dt><dd>{{ $user->department?->name }}</dd></div>
        <div class="flex justify-between px-4 py-3"><dt class="text-slate-500">Fonction</dt><dd>{{ $user->businessFunction?->name }}</dd></div>
        <div class="flex justify-between px-4 py-3"><dt class="text-slate-500">Profil</dt><dd>{{ $user->applicationRole?->name }}</dd></div>
        <div class="flex justify-between px-4 py-3"><dt class="text-slate-500">Responsable N+1</dt><dd>{{ $user->manager?->full_name ?? '—' }}</dd></div>
    </dl>

    <a href="{{ route('organisation.users.edit', $user) }}" class="mt-4 inline-block text-sm font-medium text-brand-blue hover:underline">Modifier</a>
@endsection
