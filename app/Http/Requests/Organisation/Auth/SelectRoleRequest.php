<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Auth;

use App\Models\ApplicationRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ==========================================================================
 * SelectRoleRequest
 * ==========================================================================
 *
 * AJOUT (post Étape 12, demande client) : valide le choix de rôle actif
 * fait par le User authentifié sur l'écran de sélection de rôle
 * (RoleSelectionController).
 *
 * Le rôle choisi doit être actif ET faire partie des rôles pour
 * lesquels le User authentifié est explicitement autorisé (relation N-N
 * User::authorizedRoles()) - un User ne peut pas s'auto-attribuer un
 * rôle qu'un Administrateur ne lui a pas accordé.
 * ==========================================================================
 */
class SelectRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Toujours vrai : la vérification "ce rôle m'est-il autorisé ?"
        // est faite ci-dessous, contre les propres rôles autorisés du
        // User authentifié - rien ici ne permet d'agir sur un tiers.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'application_role_id' => [
                'required',
                'integer',
                Rule::exists(ApplicationRole::class, 'id')->where('is_active', true),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'application_role_id.required' => 'Veuillez choisir un rôle.',
            'application_role_id.exists' => 'Rôle invalide.',
        ];
    }
}
