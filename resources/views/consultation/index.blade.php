<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning Hebdomadaire - Emploi du Temps</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen p-6">

    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-2xl p-8 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">📅 MON EMPLOI DU TEMPS</h1>
                    <p class="text-indigo-100 text-lg">Gestion et consultation du planning hebdomadaire</p>
                </div>
                <div class="flex gap-3">
                    <a href="/matieres/create" class="bg-white hover:bg-slate-100 text-indigo-600 font-bold px-6 py-3 rounded-lg transition transform hover:scale-105 flex items-center gap-2 shadow-lg">
                        <i class="fas fa-book"></i> Ajouter Matière
                    </a>
                    <a href="/salles/create" class="bg-white hover:bg-slate-100 text-purple-600 font-bold px-6 py-3 rounded-lg transition transform hover:scale-105 flex items-center gap-2 shadow-lg">
                        <i class="fas fa-door-open"></i> Ajouter Salle
                    </a>
                </div>
            </div>
        </div>

        <!-- Messages de succès/erreur -->
        @if(session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg p-4 mb-6 shadow-md animate-pulse">
                <div class="flex items-center">
                    <div class="flex-shrink-0 text-green-600 text-2xl">✅</div>
                    <div class="ml-4">
                        <p class="font-bold text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-lg p-4 mb-6 shadow-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0 text-red-600 text-2xl">❌</div>
                    <div class="ml-4">
                        <p class="font-bold text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulaire de programmation -->
        <div class="bg-white rounded-xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i class="fas fa-calendar-plus text-indigo-600"></i> Programmer un nouveau cours
            </h2>
            
            <form action="/programmer-cours" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @csrf
                
                <!-- Matière -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Matière</label>
                    <select name="matiere_id" class="w-full border-2 border-slate-300 rounded-lg p-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                        <option value="">-- Choisir --</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->name }} ({{ $matiere->code }})</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Salle -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Salle</label>
                    <select name="salle_id" class="w-full border-2 border-slate-300 rounded-lg p-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                        <option value="">-- Choisir --</option>
                        @foreach($salles as $salle)
                            <option value="{{ $salle->id }}">{{ $salle->nom }} ({{ $salle->capacite }} places)</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Jour -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jour</label>
                    <select name="jour" class="w-full border-2 border-slate-300 rounded-lg p-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                        <option value="lundi">Lundi</option>
                        <option value="mardi">Mardi</option>
                        <option value="mercredi">Mercredi</option>
                        <option value="jeudi">Jeudi</option>
                        <option value="vendredi">Vendredi</option>
                        <option value="samedi">Samedi</option>
                    </select>
                </div>
                
                <!-- Horaire -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Horaire</label>
                    <select name="horaire" class="w-full border-2 border-slate-300 rounded-lg p-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                        <option value="08H-10H">08H - 10H</option>
                        <option value="10H-12H">10H - 12H</option>
                        <option value="14H-16H">14H - 16H</option>
                        <option value="16H-18H">16H - 18H</option>
                    </select>
                </div>
                
                <!-- Bouton -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg transition transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Programmer
                    </button>
                </div>
            </form>
        </div>

        <!-- Tableau d'emploi du temps -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-slate-800 to-slate-900 p-6">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-table text-indigo-400"></i> Emploi du Temps Hebdomadaire
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-100 border-b-2 border-slate-300">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold text-slate-800 bg-slate-200">HORAIRES</th>
                            <th class="px-6 py-4 text-center font-bold text-slate-800">LUNDI</th>
                            <th class="px-6 py-4 text-center font-bold text-slate-800">MARDI</th>
                            <th class="px-6 py-4 text-center font-bold text-slate-800">MERCREDI</th>
                            <th class="px-6 py-4 text-center font-bold text-slate-800">JEUDI</th>
                            <th class="px-6 py-4 text-center font-bold text-slate-800">VENDREDI</th>
                            <th class="px-6 py-4 text-center font-bold text-slate-800">SAMEDI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $horaires = ['08H-10H', '10H-12H', '14H-16H', '16H-18H'];
                            $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
                        @endphp
                        
                        @foreach($horaires as $index => $horaire)
                        <tr class="border-b border-slate-200 hover:bg-indigo-50 transition">
                            <td class="px-6 py-4 font-bold text-slate-700 bg-slate-50">{{ $horaire }}</td>
                            
                            @foreach($jours as $jour)
                            <td class="px-6 py-4 text-center">
                                @if(isset($emploi[$jour][$horaire]))
                                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-300 rounded-lg p-3 shadow-md hover:shadow-lg transition">
                                        <p class="font-bold text-indigo-900 text-sm">{{ $emploi[$jour][$horaire]['matiere'] }}</p>
                                        <p class="text-xs text-indigo-700 font-semibold">{{ $emploi[$jour][$horaire]['code'] }}</p>
                                        <p class="text-xs text-indigo-600 mt-1">
                                            <i class="fas fa-door-open"></i> {{ $emploi[$jour][$horaire]['salle'] }}
                                        </p>
                                    </div>
                                @else
                                    @if($jour == 'samedi')
                                        <span class="text-slate-400 text-sm italic">Repos</span>
                                    @else
                                        <span class="text-slate-300 text-sm italic">Libre</span>
                                    @endif
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="text-center mt-8 text-slate-400 text-sm">
            <p>Système de Gestion d'Emploi du Temps - 2026</p>
        </div>
    </div>

</body>
</html>