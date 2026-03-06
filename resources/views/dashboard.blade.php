<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }} — 
            @if(Auth::user()->role === 'admin') Admin @elseif(Auth::user()->role === 'teacher') Enseignant @else Étudiant @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- BLOC CENTRAL DYNAMIQUE --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 p-10 text-center">
                
                {{-- 1. ICÔNE ET COULEUR SELON LE RÔLE --}}
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 
                    {{ Auth::user()->role === 'admin' ? 'bg-purple-100' : (Auth::user()->role === 'teacher' ? 'bg-emerald-100' : 'bg-indigo-100') }}">
                    
                    @if(Auth::user()->role === 'admin')
                        <i class="fas fa-user-shield text-3xl text-purple-600"></i>
                    @elseif(Auth::user()->role === 'teacher')
                        <i class="fas fa-chalkboard-teacher text-3xl text-emerald-600"></i>
                    @else
                        <i class="fas fa-user-graduate text-3xl text-indigo-600"></i>
                    @endif
                </div>

                {{-- 2. MESSAGE DE BIENVENUE --}}
                <h1 class="text-3xl font-black text-gray-900 mb-4">
                    Bienvenue, {{ Auth::user()->name }} !
                </h1>
                
                <p class="text-gray-600 text-lg mb-8 max-w-2xl mx-auto">
                    @if(Auth::user()->role === 'admin')
                        Vous avez le contrôle total sur les utilisateurs, les salles et la programmation des cours de l'ESGIS.
                    @elseif(Auth::user()->role === 'teacher')
                        Consultez vos horaires de cours et gérez vos séances pédagogiques en toute simplicité.
                    @else
                        Accédez à votre emploi du temps hebdomadaire et suivez votre progression académique.
                    @endif
                </p>

                {{-- 3. BOUTONS D'ACTION SELON LE RÔLE --}}
                <div class="flex flex-wrap justify-center gap-4">
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.users.index') }}" class="bg-purple-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-purple-700 transition">
                            <i class="fas fa-users mr-2"></i> Gérer les Utilisateurs
                        </a>
                        <a href="{{ route('referential.index') }}" class="bg-white border-2 border-purple-600 text-purple-600 px-6 py-3 rounded-xl font-bold hover:bg-purple-50 transition">
                            <i class="fas fa-database mr-2"></i> Référentiel
                        </a>
                    @elseif(Auth::user()->role === 'teacher')
                        <a href="{{ route('dashboard.emploi') }}" class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-emerald-700 transition">
                            <i class="fas fa-calendar-alt mr-2"></i> Acceder au Planning
                        </a>
                    @else
                        <a href="{{ route('consultation.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-calendar-check mr-2"></i> Mon Emploi du Temps
                        </a>
                    @endif
                </div>
            </div>

            {{-- 4. PETITES CARTES DE STATUT (BAS DE PAGE) --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 font-medium italic">Statut du compte</p>
                    <p class="text-lg font-bold {{ Auth::user()->role === 'admin' ? 'text-purple-600' : 'text-indigo-600' }}">
                        {{ ucfirst(Auth::user()->role) }} Actif
                    </p>
                </div>
                </div>

        </div>
    </div>
</x-app-layout>