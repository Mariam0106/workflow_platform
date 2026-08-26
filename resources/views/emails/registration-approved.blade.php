@component('mail::message')
# Bienvenue, {{ $user->first_name }} !

Ta demande d'inscription sur **Workflow Platform** a été **approuvée** par un Administrateur.

Tu peux maintenant te connecter avec l'adresse e-mail et le mot de passe que tu as choisis à l'inscription.

@component('mail::button', ['url' => route('login')])
Me connecter
@endcomponent

Si tu n'es pas à l'origine de cette demande, contacte immédiatement un Administrateur.

Merci,<br>
{{ config('app.name') }}
@endcomponent
