@component('mail::message')
# Bonjour {{ $user->first_name }},

Votre demande d'inscription sur **Workflow Platform** n'a pas été retenue par {{ $user->approver?->full_name ?? "l'Administrateur qui l'a examinée" }}.

@if ($user->rejected_reason)
**Motif indiqué :**
{{ $user->rejected_reason }}
@endif

Si vous pensez qu'il s'agit d'une erreur, contactez directement votre service informatique.

Merci,<br>
{{ config('app.name') }}
@endcomponent