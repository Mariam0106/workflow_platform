@extends('layouts.admin', ['title' => 'Nouvelle demande'])

@section('content')
    <x-page-header title="Nouvelle demande" description="Choisissez le formulaire correspondant à votre demande.">
        <x-slot:actions>
            <x-button href="{{ route('workflow.my-requests.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($forms->isEmpty())
        <x-card>
            <x-empty-state icon="file" title="Aucun formulaire disponible" description="Aucun formulaire n'est publié pour le moment. Contactez votre administrateur." />
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($forms as $form)
                <a href="{{ route('workflow.my-requests.create', $form) }}"
                   class="flex flex-col gap-1.5 rounded-xl border border-brand-border bg-white p-4 transition hover:border-brand-blue/40 hover:shadow-sm">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-blue/10 text-brand-blue">
                        @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-4 w-4'])
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="font-medium text-brand-navy">{{ $form->name }}</span>
                        <x-priority-badge :priority="$form->priority" />
                    </span>
                    <span class="text-xs text-slate-500">{{ $form->formCategory?->name }}</span>
                    @if ($form->description)
                        <span class="mt-1 line-clamp-2 text-[13px] text-slate-500">{{ $form->description }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endsection
