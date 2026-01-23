<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
     $request->authenticate();

    $request->session()->regenerate();

    // --- LOGIQUE DE REDIRECTION PERSONNALISÉE ---
    $user = $request->user();

    if ($user->role_id == 1) { // Admin
        return redirect()->route('admin.dashboard');
    } 
    
    if ($user->role_id == 2) { // Enseignant
        return redirect()->route('teacher.dashboard');
    }

    // Par défaut pour les Étudiants (role_id 3 ou autre)
    return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
