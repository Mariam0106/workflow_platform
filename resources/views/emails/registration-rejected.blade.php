@component('mail::message')
# Bonjour {{ $user->first_name }},

Ta demande d'inscription sur **Workflow Platform** n'a pas été retenue par l'Administrateur qui l'a examinée.

@if ($user->rejected_reason)
**Motif indiqué :**
{{ $user->rejected_reason }}
@endif

Si tu penses qu'il s'agit d'une erreur, contacte directement ton service informatique.

Merci,<br>
{{ config('app.name') }}
@endcomponent
