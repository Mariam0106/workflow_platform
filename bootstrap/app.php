<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Uniformise le format JSON des refus de Policy (Illuminate\Auth\
        // Access\AuthorizationException, levee par $this->authorize())
        // avec celui de nos DomainException (Etape 5) : {"error": "...",
        // "message": "..."}. Sans ca, une API cliente devrait gerer 2
        // formats d'erreur differents selon que le refus vienne d'une
        // Policy ou d'un Service - incoherent pour une app entreprise.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'unauthorized_action',
                    'message' => $e->getMessage(),
                ], 403);
            }
        });
    })->create();
