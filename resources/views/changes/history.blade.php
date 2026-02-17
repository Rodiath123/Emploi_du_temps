@extends('layouts.app')

@section('title', 'Historique - ' . class_basename($model))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">← Retour</a>
        <h1 class="text-4xl font-bold text-gray-900">Historique des Modifications</h1>
        <p class="text-gray-500 mt-2">{{ class_basename($model) }} #{{ $model->id }}</p>
    </div>

    <!-- Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-gray-900">{{ $summary['total_changes'] }}</div>
            <div class="text-gray-500 text-sm mt-1">Total modifications</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-blue-600">{{ $summary['changes_by_action']['created'] }}</div>
            <div class="text-gray-500 text-sm mt-1">Créations</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-green-600">{{ $summary['changes_by_action']['updated'] }}</div>
            <div class="text-gray-500 text-sm mt-1">Modifications</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-red-600">{{ $summary['changes_by_action']['deleted'] }}</div>
            <div class="text-gray-500 text-sm mt-1">Suppressions</div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Chronologie des Modifications</h2>
        
        <div class="space-y-4">
            @forelse($history as $change)
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                {{ $change->action_label }}
                                @if($change->field_name)
                                    <span class="text-gray-500">{{ $change->field_name }}</span>
                                @endif
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                par <strong>{{ $change->user?->name ?? 'Système' }}</strong>
                                le <strong>{{ $change->created_at->format('d/m/Y à H:i') }}</strong>
                            </p>
                        </div>
                        <a href="{{ route('changes.comparison', $change) }}" class="text-blue-600 hover:text-blue-900">
                            Détails →
                        </a>
                    </div>
                    
                    @if($change->action === 'updated' && $change->old_value)
                        <div class="mt-2 bg-gray-50 p-3 rounded text-sm">
                            <div class="flex gap-4">
                                <div>
                                    <span class="text-red-600 font-medium">Avant:</span>
                                    <span class="text-gray-700">{{ \Illuminate\Support\Str::limit($change->old_value, 50) }}</span>
                                </div>
                                <div>
                                    <span class="text-green-600 font-medium">Après:</span>
                                    <span class="text-gray-700">{{ \Illuminate\Support\Str::limit($change->new_value, 50) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">Aucune modification enregistrée</p>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($history->hasPages())
        <div class="mt-6">
            {{ $history->links() }}
        </div>
    @endif
</div>
@endsection
