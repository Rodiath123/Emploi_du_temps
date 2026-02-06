<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <body class="bg-gray-100 p-10">
    <div class="max-w-md mx-auto bg-white p-8 rounded shadow">
        <h2 class="text-xl font-bold mb-6">➕ Ajouter une nouvelle crenau</h2>
<form action="/creneaux/store" method="POST">
    @csrf
    <div class="mb-4">
        <label>Matière</label>
        <select name="matiere_id" required>
            @foreach($matieres as $matiere)
                <option value="{{ $matiere->id }}">{{ $matiere->name }} ({{ $matiere->code }})</option>
            @endforeach
        </select>
    </div>
    
    <div class="mb-4">
        <label>Salle</label>
        <select name="salle_id" required>
            @foreach($salles as $salle)
                <option value="{{ $salle->id }}">{{ $salle->nom }} ({{ $salle->capacite }} places)</option>
            @endforeach
        </select>
    </div>
    
    <div class="mb-4">
        <label>Jour</label>
        <select name="jour" required>
            <option value="lundi">Lundi</option>
            <option value="mardi">Mardi</option>
            <option value="mercredi">Mercredi</option>
            <option value="jeudi">Jeudi</option>
            <option value="vendredi">Vendredi</option>
            <option value="samedi">Samedi</option>
        </select>
    </div>
    
    <div class="mb-4">
        <label>Créneau horaire</label>
        <select name="heure_debut" required>
            <option value="08:00">08H - 10H</option>
            <option value="10:00">10H - 12H</option>
            <option value="14:00">14H - 16H</option>
            <option value="16:00">16H - 18H</option>
        </select>
    </div>
    
    <button type="submit">Ajouter au planning</button>
</form>

    </body>
</html>