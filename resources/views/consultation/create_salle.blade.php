<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Salle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-lg">
        <!-- Retour -->
        <a href="/mon-emploi-du-temps" class="inline-flex items-center gap-2 text-green-400 hover:text-green-300 mb-8 transition">
            <i class="fas fa-arrow-left"></i> Retour au planning
        </a>

        <div class="bg-white rounded-2xl shadow-2xl p-10 border-t-4 border-green-500">
            <!-- En-tête -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-door-open text-green-600 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-black text-slate-800">Nouvelle Salle</h1>
                <p class="text-slate-500 mt-2">Ajouter une salle ou amphithéâtre</p>
            </div>
            
            <!-- Formulaire -->
            <form action="/salles/store" method="POST" class="space-y-6">
                @csrf
                
                <!-- Nom de la salle -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">
                        <i class="fas fa-building text-green-600"></i> Nom de la salle
                    </label>
                    <input 
                        type="text" 
                        name="nom" 
                        placeholder="Ex: Amphi A, Salle B2, Amphi C..."
                        class="w-full border-2 border-slate-300 rounded-lg p-4 text-slate-700 placeholder-slate-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition duration-200 font-medium"
                        required
                    >
                    @error('nom')
                        <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Capacité -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">
                        <i class="fas fa-users text-green-600"></i> Capacité (nombre de places)
                    </label>
                    <input 
                        type="number" 
                        name="capacite" 
                        placeholder="Ex: 30, 50, 100..."
                        class="w-full border-2 border-slate-300 rounded-lg p-4 text-slate-700 placeholder-slate-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition duration-200 font-medium"
                        min="1"
                        max="500"
                        required
                    >
                    @error('capacite')
                        <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Boutons -->
                <div class="flex gap-3 pt-4">
                    <a href="/mon-emploi-du-temps" class="flex-1 border-2 border-slate-300 hover:border-slate-400 text-slate-700 font-bold py-3 px-4 rounded-lg transition duration-200 text-center">
                        Annuler
                    </a>
                    <button 
                        type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 flex items-center justify-center gap-2 shadow-lg"
                    >
                        <i class="fas fa-check"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Astuce -->
        <div class="bg-green-900 bg-opacity-50 border border-green-700 rounded-lg p-4 mt-6 text-center">
            <p class="text-green-200 text-sm">
                <i class="fas fa-lightbulb text-yellow-400"></i> La capacité doit être entre 1 et 500 places
            </p>
        </div>
    </div>

</body>
</html>