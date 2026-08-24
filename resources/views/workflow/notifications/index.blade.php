@extends('layouts.admin', ['title' => 'Notifications'])

@section('content')
    <x-page-header title="Notifications" description="{{ $notifications->total() }} notification(s)" />

    <x-card :padded="false">
        @if ($notifications->isEmpty())
            <x-empty-state icon="bell" title="Aucune notification" />
        @else
            <ul class="divide-y divide-brand-border">
                @foreach ($notifications as $notification)
                    <li class="flex items-start gap-3 px-5 py-4 {{ $notification->isRead() ? '' : 'bg-brand-blue/[0.03]' }}">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-blue/10 text-brand-blue">
                            @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-4 w-4'])
                        </span>
                        <div class="min-w-0 flex-1">
                            @if ($notification->request && \Illuminate\Support\Facades\Gate::allows('view', $notification->request))
                                <a href="{{ route('workflow.my-requests.show', $notification->request) }}" class="text-sm font-medium text-brand-navy hover:text-brand-blue">
                                    {{ $notification->title }}
                                </a>
                            @else
                                <p class="text-sm font-medium text-brand-navy">{{ $notification->title }}</p>
                            @endif
                            <p class="mt-0.5 text-[13px] text-slate-600">{{ $notification->message }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @unless ($notification->isRead())
                            <form method="POST" action="{{ route('workflow.notifications.read', $notification) }}">
                                @csrf
                                <x-button type="submit" variant="ghost" size="sm">Marquer comme lue</x-button>
                            </form>
                        @endunless
                    </li>
                @endforeach
            </ul>
        @endif
        <x-simple-paginator :paginator="$notifications" />
    </x-card>
@endsection
