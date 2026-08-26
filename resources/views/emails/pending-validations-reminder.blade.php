@component('mail::message')
# Bonjour {{ $validator->first_name }},

@if ($pendingRequests->count() === 1)
Une demande attend toujours votre décision sur **Workflow Platform**.
@else
**{{ $pendingRequests->count() }} demandes** attendent toujours votre décision sur **Workflow Platform**.
@endif

@component('mail::table')
| Référence | Formulaire | Soumise le |
|:----------|:-----------|:-----------|
@foreach ($pendingRequests->take(10) as $request)
| {{ $request->reference_number }} | {{ $request->form?->name }} | {{ $request->submitted_at?->format('d/m/Y') }} |
@endforeach
@endcomponent

@if ($pendingRequests->count() > 10)
*... et {{ $pendingRequests->count() - 10 }} autre(s).*
@endif

@component('mail::button', ['url' => route('workflow.my-validations.index')])
Voir mes validations
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
