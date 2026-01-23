<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role_slug): Response
{
        // On vérifie si l'utilisateur est connecté et si son rôle correspond au slug attendu
    if (!$request->user() || $request->user()->role->slug !== $role_slug) {
        abort(403, 'Accès non autorisé.');
    }
        return $next($request);
    }
}
