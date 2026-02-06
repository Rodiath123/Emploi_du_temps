<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChange
{
   public function handle(Request $request, Closure $next): Response
{
    // On vérifie d'abord si l'utilisateur est connecté
    if (auth()->check()) {
        $user = auth()->user();

        // Si l'utilisateur DOIT changer son mot de passe
        if ($user->must_change_password) {
            
            // On définit les routes autorisées
            $allowedRoutes = [
                'profile.edit',
                'profile.update',
                'password.update',
                'logout',
            ];

            // Si la route actuelle n'est pas dans la liste, on redirige
            if (!$request->routeIs($allowedRoutes)) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'Sécurité : Veuillez définir un mot de passe personnel pour débloquer votre accès.');
            }
        }
    }

    return $next($request);
}
}