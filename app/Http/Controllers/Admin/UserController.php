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
    'password' => Hash::make($request->password), // Mot de passe par défaut (ex: Esgis2026)
    'role' => $request->role,
    'must_change_password' => true, // <--- On force le changement au prochain login
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Utilisateur supprimé avec succès.');
    }
}