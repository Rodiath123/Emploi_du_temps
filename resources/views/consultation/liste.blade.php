<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Référentiel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">📋 Gestion du Référentiel</h1>

        <div class="bg-white p-6 rounded shadow mb-10">
            <h2 class="text-xl font-semibold mb-4 text-blue-600">Salles</h2>
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">Nom</th>
                        <th class="py-2">Capacité</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salles as $salle)
                    <tr class="border-b">
                        <td class="py-2">{{ $salle->nom }}</td>
                        <td class="py-2">{{ $salle->capacite }} places</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="/salles/create" class="mt-4 inline-block text-blue-500 underline">+ Ajouter une salle</a>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-4 text-green-600">Matières</h2>
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">Code</th>
                        <th class="py-2">Nom</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matieres as $matiere)
                    <tr class="border-b">
                        <td class="py-2 font-mono text-sm">{{ $matiere->code }}</td>
                        <td class="py-2">{{ $matiere->nom }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="/matieres/create" class="mt-4 inline-block text-green-500 underline">+ Ajouter une matière</a>
        </div>
    </div>
</body>
</html>