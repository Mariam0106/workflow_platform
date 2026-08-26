@extends('layouts.admin', ['title' => 'Nouvelle demande'])

@section('content')
    @if ($category === null)
        {{-- ==================================================
             Étape 1 : choix de la Catégorie - évite d'afficher
             tous les Formulaires publiés d'un coup, ce qui devient
             vite illisible dès qu'il y en a beaucoup.
        =================================================== --}}
        <x-page-header title="Nouvelle demande" description="Choisissez la catégorie correspondant à votre demande.">
            <x-slot:actions>
                <x-button href="{{ route('workflow.my-requests.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
            </x-slot:actions>
        </x-page-header>

        @if ($categories->isEmpty())
            <x-card>
                <x-empty-state icon="layers" title="Aucun formulaire disponible" description="Aucun formulaire n'est publié pour le moment. Contactez votre administrateur." />
            </x-card>
        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $formCategory)
                    <a href="{{ route('workflow.my-requests.select-form', ['category' => $formCategory->id]) }}"
                       class="flex flex-col gap-1.5 rounded-xl border border-brand-border bg-white p-4 transition hover:border-brand-blue/40 hover:shadow-sm">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-blue/10 text-brand-blue">
                            @include('layouts.partials.icon', ['name' => 'layers', 'class' => 'h-4 w-4'])
                        </span>
                        <span class="font-medium text-brand-navy">{{ $formCategory->name }}</span>
                        <span class="text-xs text-slate-500">{{ $formCategory->forms_count }} formulaire(s)</span>
                        @if ($formCategory->description)
                            <span class="mt-1 line-clamp-2 text-[13px] text-slate-500">{{ $formCategory->description }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    @else
        {{-- ==================================================
             Étape 2 : formulaires de la Catégorie choisie.
        =================================================== --}}
        <x-page-header :title="$category->name" description="Choisissez le formulaire correspondant à votre demande.">
            <x-slot:actions>
                <x-button href="{{ route('workflow.my-requests.select-form') }}" variant="secondary" icon="arrow-left">Changer de catégorie</x-button>
            </x-slot:actions>
        </x-page-header>

        <form method="GET" class="relative mb-4 w-full max-w-xs">
            <input type="hidden" name="category" value="{{ $category->id }}">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
            </span>
            <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher un formulaire…" autocomplete="off"
                   class="h-9 w-full rounded-lg border border-brand-border bg-white pl-9 pr-3 text-[13px] text-brand-navy shadow-sm transition placeholder:text-slate-400 hover:border-brand-blue/40 focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
        </form>

        @if ($forms->isEmpty())
            <x-card>
                <x-empty-state icon="file" :title="$search ? 'Aucun formulaire ne correspond' : 'Aucun formulaire dans cette catégorie'" />
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
                        @if ($form->description)
                            <span class="mt-1 line-clamp-2 text-[13px] text-slate-500">{{ $form->description }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    @endif
@endsection
