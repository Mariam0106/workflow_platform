<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Écran "Mon profil" - accessible à tout Utilisateur authentifié pour
 * lui-même uniquement (jamais pour consulter un tiers, ça reste le
 * rôle de l'écran Admin "Utilisateurs"). Lecture seule ici - modifier
 * son profil reste une action Admin (BR-75), conformément au cahier
 * des charges.
 */
class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load([
            'entity', 'department', 'businessFunction', 'manager', 'applicationRoles',
        ]);

        return view('organisation.profile.show', ['user' => $user]);
    }
}
