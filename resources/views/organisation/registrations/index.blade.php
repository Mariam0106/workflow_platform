@extends('layouts.admin', ['title' => 'Inscriptions en attente'])

@section('content')
    <x-page-header title="Inscriptions en attente" :description="$pending->count() . ' demande(s) à traiter'" />

    <x-card :padded="false">
        @if ($pending->isEmpty())
            <x-empty-state icon="users" title="Aucune demande en attente" description="Toutes les inscriptions ont été traitées." />
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Demandeur</th>
                        <th class="px-5 py-3">Rattachement demandé</th>
                        <th class="px-5 py-3">Envoyée le</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($pending as $registration)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-brand-navy">{{ $registration->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $registration->email }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $registration->entity?->name }} · {{ $registration->department?->name }}
                                <br><span class="text-xs text-slate-400">{{ $registration->businessFunction?->name }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <x-button href="{{ route('organisation.registrations.edit', $registration) }}" size="sm" icon="check">
                                    Examiner
                                </x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-card>
@endsection
