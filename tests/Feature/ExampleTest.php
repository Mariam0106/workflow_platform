<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * "/" n'affiche plus la page Laravel par défaut - un visiteur non
     * connecté est redirigé vers l'écran de connexion.
     */
    public function test_a_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    /**
     * Un Utilisateur déjà connecté qui revient sur "/" est redirigé
     * directement vers son tableau de bord, pas vers le login.
     */
    public function test_an_authenticated_user_is_redirected_to_the_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/');

        $response->assertRedirect(route('dashboard'));
    }
}
