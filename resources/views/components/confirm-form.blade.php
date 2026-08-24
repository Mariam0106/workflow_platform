@props(['action', 'method' => 'POST', 'confirm' => 'Confirmer cette action ?', 'variant' => 'secondary', 'icon' => null, 'title' => null])

<form method="POST" action="{{ $action }}" onsubmit="return confirm({{ json_encode($confirm) }});">
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif
    <x-button type="submit" :variant="$variant" size="sm" :icon="$icon" :title="$title">
        {{ $slot }}
    </x-button>
</form>