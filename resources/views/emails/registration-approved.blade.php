@component('mail::message')
# Bienvenue, {{ $user->first_name }} !

Votre demande d'inscription sur **Workflow Platform** a été **approuvée** par {{ $user->approver?->full_name ?? 'un Administrateur' }}.

Vous pouvez maintenant vous connecter avec l'adresse e-mail et le mot de passe choisis lors de votre inscription.

@component('mail::button', ['url' => route('login')])
Me connecter
@endcomponent

Si vous n'êtes pas à l'origine de cette demande, contactez immédiatement un Administrateur.

Merci,<br>
{{ config('app.name') }}
@endcomponent