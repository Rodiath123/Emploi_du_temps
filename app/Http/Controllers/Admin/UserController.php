<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Affiche la liste de tous les utilisateurs
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Met à jour le rôle d'un utilisateur
    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,teacher,student']);
        
        $user->update(['role' => $request->role]);

        return back()->with('status', 'Le rôle de ' . $user->name . ' a été mis à jour en : ' . $request->role);
    }
}