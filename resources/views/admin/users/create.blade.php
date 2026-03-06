<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un nouvel utilisateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Bouton Retour --}}
            <div class="mb-6">
                <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Retour à la liste
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-slate-800">Informations Personnelles</h3>
                        <p class="text-sm text-slate-500">Créez un compte pour un étudiant, un enseignant ou un administrateur.</p>
                    </div>

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Nom Complet --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Nom Complet</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                                    </span>
                                    <input type="text" name="name" required
                                        class="w-full pl-10 pr-4 py-3 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition"
                                        placeholder="Ex: Fadilath Adegnile">
                                </div>
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Adresse Email</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-width="2"/></svg>
                                    </span>
                                    <input type="email" name="email" required
                                        class="w-full pl-10 pr-4 py-3 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition"
                                        placeholder="exemple@esgis.bj">
                                </div>
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                           <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Rôle --}}
    <div class="md:col-span-2"> {{-- On met le rôle sur toute la largeur ou avant les mots de passe --}}
        <label class="block text-sm font-semibold text-slate-700 mb-2">Rôle Système</label>
        <select name="role" required
            class="w-full border-slate-200 rounded-xl py-3 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
            <option value="student">Étudiant</option>
            <option value="teacher">Enseignant</option>
            <option value="admin">Administrateur</option>
        </select>
    </div>

    {{-- Mot de passe --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Mot de passe provisoire</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <input type="password" name="password" required
                class="w-full pl-10 pr-4 py-3 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition"
                placeholder="••••••••">
        </div>
        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Confirmation --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Confirmer le mot de passe</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <input type="password" name="password_confirmation" required
                class="w-full pl-10 pr-4 py-3 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition"
                placeholder="••••••••">
        </div>
    </div>
</div>

                        <div class="mt-10 pt-6 border-t border-slate-100 flex justify-end gap-3">
                            <button type="reset" class="px-6 py-3 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition">
                                Réinitialiser
                            </button>
                            <button type="submit" 
                                class="px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-lg hover:shadow-indigo-200 transition transform hover:scale-105">
                                Créer l'utilisateur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>