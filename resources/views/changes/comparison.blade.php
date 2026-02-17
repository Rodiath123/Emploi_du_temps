@extends('layouts.app')

@section('title', 'Comparaison - Modification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">← Retour</a>
    
    <div class="bg-white rounded-lg shadow p-8">
        <div class="mb-8 pb-6 border-b">
            <h1 class="text-3xl font-bold text-gray-900">Détails de la Modification</h1>
            <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                <div>
                    <span class="text-gray-500">Type de Modification:</span>
                    <span class="font-semibold text-gray-900 ml-2">{{ $comparison['action'] }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Date/Heure:</span>
                    <span class="font-semibold text-gray-900 ml-2">{{ $comparison['changed_at'] }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Champ:</span>
                    <span class="font-semibold text-gray-900 ml-2">{{ $comparison['field'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Modifié par:</span>
                    <span class="font-semibold text-gray-900 ml-2">
                        @if($comparison['changed_by'])
                            {{ $comparison['changed_by']['name'] }}
                        @else
                            Système
                        @endif
                    </span>
                </div>
            </div>
        </div>

        @if($comparison['field'])
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <h2 class="text-lg font-semibold text-red-600 mb-4">Valeur Précédente</h2>
                    <div class="bg-red-50 p-4 rounded border border-red-200 font-mono text-sm">
                        @if($comparison['before'] === null)
                            <span class="text-gray-500 italic">[Vide]</span>
                        @else
                            {{ is_array($comparison['before']) ? json_encode($comparison['before'], JSON_PRETTY_PRINT) : $comparison['before'] }}
                        @endif
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-green-600 mb-4">Nouvelle Valeur</h2>
                    <div class="bg-green-50 p-4 rounded border border-green-200 font-mono text-sm">
                        @if($comparison['after'] === null)
                            <span class="text-gray-500 italic">[Vide]</span>
                        @else
                            {{ is_array($comparison['after']) ? json_encode($comparison['after'], JSON_PRETTY_PRINT) : $comparison['after'] }}
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Metadata -->
        @if($comparison['changed_by'])
            <div class="bg-blue-50 p-4 rounded border border-blue-200 text-sm mb-6">
                <h3 class="font-semibold text-blue-900 mb-2">Informations de Traçabilité</h3>
                <dl class="space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-blue-700">Utilisateur:</dt>
                        <dd class="text-blue-900 font-semibold">{{ $comparison['changed_by']['name'] }} ({{ $comparison['changed_by']['email'] }})</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-blue-700">Adresse IP:</dt>
                        <dd class="text-blue-900 font-family-mono">{{ $comparison['ip_address'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-blue-700">Agent Utilisateur:</dt>
                        <dd class="text-blue-900 text-xs truncate">{{ \Illuminate\Support\Str::limit($comparison['user_agent'] ?? 'N/A', 60) }}</dd>
                    </div>
                </dl>
            </div>
        @endif

        <div class="bg-gray-50 p-4 rounded border border-gray-200 text-xs text-gray-600">
            <strong>ID de Modification:</strong> #{{ $comparison['id'] }}
        </div>
    </div>
</div>
@endsection
