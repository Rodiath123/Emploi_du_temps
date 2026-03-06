<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Matière</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-lg">
        <!-- Retour -->
        <a href="/mon-emploi-du-temps" class="inline-flex items-center gap-2 text-indigo-400 hover:text-indigo-300 mb-8 transition">
            <i class="fas fa-arrow-left"></i> Retour au planning
        </a>

        <div class="bg-white rounded-2xl shadow-2xl p-10 border-t-4 border-indigo-600">
            <!-- En-tête -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
                    <i class="fas fa-book text-indigo-600 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-black text-slate-800">Nouvelle Matière</h1>
                <p class="text-slate-500 mt-2">Ajouter une matière au système</p>
            </div>
            
            <!-- Formulaire -->
            <form action="/matieres/store" method="POST" class="space-y-6">
                @csrf
                
                <!-- Nom de la matière -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">
                        <i class="fas fa-tag text-indigo-600"></i> Nom de la matière
                    </label>
                    <input 
                        type="text" 
                        name="nom" 
                        placeholder="Ex: Mathématiques, Physique, Informatique..."
                        class="w-full border-2 border-slate-300 rounded-lg p-4 text-slate-700 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-200 font-medium"
                        required
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Code -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">
                        <i class="fas fa-hashtag text-indigo-600"></i> Code
                    </label>
                    <input 
                        type="text" 
                        name="code" 
                        placeholder="Ex: MAT101, PHY201, INF301..."
                        class="w-full border-2 border-slate-300 rounded-lg p-4 text-slate-700 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-200 font-medium"
                        required
                    >
                    @error('code')
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
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 flex items-center justify-center gap-2 shadow-lg"
                    >
                        <i class="fas fa-check"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Astuce -->
        <div class="bg-indigo-900 bg-opacity-50 border border-indigo-700 rounded-lg p-4 mt-6 text-center">
            <p class="text-indigo-200 text-sm">
                <i class="fas fa-lightbulb text-yellow-400"></i> Le code doit être unique et facilement identifiable
            </p>
        </div>
    </div>

</body>
</html>