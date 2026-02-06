<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Utilisateurs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle Actuel</th>
                            <th>Modifier le Rôle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300">
                                        <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>Étudiant</option>
                                        <option value="teacher" {{ $user->role == 'teacher' ? 'selected' : '' }}>Enseignant</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>