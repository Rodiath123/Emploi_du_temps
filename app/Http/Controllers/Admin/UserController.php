<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        // On remet la récupération de TOUS les utilisateurs sans exception
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,teacher,student'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'must_change_password' => true, 
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "L'utilisateur {$request->name} a été créé avec succès.");
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,teacher,student',
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()
            ->with('success', "Le rôle de {$user->name} a été mis à jour.");
    }

    public function destroy(User $user)
    {
        // Sécurité minimale : on empêche quand même la suppression de soi-même 
        // pour ne pas que tu te fasses "auto-expulser" par erreur.
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Action impossible sur votre propre compte.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Utilisateur supprimé avec succès.');
    }
}