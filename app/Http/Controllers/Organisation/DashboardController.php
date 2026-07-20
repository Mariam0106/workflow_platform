<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ==========================================================================
 * DashboardController (placeholder)
 * ==========================================================================
 *
 * Minimal stand-in so Jalon J1 has a real protected route to redirect to
 * after login. The actual dashboard (KPIs, activités utilisateur...) is
 * built at l'Étape 14 - BackOffice - this controller/view will be replaced
 * then, not extended.
 * ==========================================================================
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('dashboard', [
            'user' => $request->user(),
        ]);
    }
}
