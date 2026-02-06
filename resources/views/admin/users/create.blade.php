<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un nouvel utilisateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Nom Complet</label>
                            <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Email</label>
                            <input type="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Rôle</label>
                            <select name="role" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="student">Étudiant</option>
                                <option value="teacher">Enseignant</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Mot de passe</label>
                            <input type="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Confirmer mot de passe</label>
                            <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Enregistrer l'utilisateur
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>