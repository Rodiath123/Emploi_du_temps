@extends('layouts.app')

@section('title', 'Historique des Modifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Historique des Modifications</h1>
        <p class="text-gray-500 mt-2">Suivi complet des changements et des modifications dans le système</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <h2 class="text-xl font-semibold mb-4">Filtres</h2>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type d'Action</label>
                <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500">
                    <option value="">Tous</option>
                    <option value="created" @selected(request('action') === 'created')>Créé</option>
                    <option value="updated" @selected(request('action') === 'updated')>Modifié</option>
                    <option value="deleted" @selected(request('action') === 'deleted')>Supprimé</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Début</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Fin</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                    Appliquer les Filtres
                </button>
            </div>
        </form>
    </div>

    <!-- Changes List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Modèle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Champ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($changes as $change)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $change->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($change->user)
                                <span class="font-medium">{{ $change->user->name }}</span>
                                <span class="text-gray-500 text-xs">{{ $change->user->email }}</span>
                            @else
                                <span class="text-gray-500 italic">Système</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($change->action === 'created') bg-green-100 text-green-800
                                @elseif($change->action === 'updated') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $change->action_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ class_basename($change->model_type) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $change->field_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('changes.comparison', $change) }}" 
                                class="text-blue-600 hover:text-blue-900 font-medium">
                                Détails
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Aucune modification trouvée
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($changes->hasPages())
        <div class="mt-6">
            {{ $changes->links() }}
        </div>
    @endif
</div>
@endsection
