@php
 
$user = Auth::user();
    $role = $user->role ?? 'user'; 

    // On définit la route du Dashboard (le message de bienvenue) selon le rôle
    if ($role === 'admin') {
        $dashboardRoute = 'admin.dashboard';
    } elseif ($role === 'teacher') {
        $dashboardRoute = 'teacher.dashboard';
    } else {
        $dashboardRoute = 'dashboard'; // Pour l'étudiant
    }

    // Helper active
    $isRoute = fn(string $pattern) => request()->routeIs($pattern);
 
@endphp

<div x-data="{ sidebarOpen: false, userOpen: false }" class="min-h-screen bg-gray-50">
 {{-- HEADER --}}
 <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-gray-200">
 <div class="w-full px-6">
 <div class="flex h-16 items-center justify-between gap-4">

 {{-- Left: burger + logo --}}
 <div class="flex items-center gap-3">
 <button
 @click="sidebarOpen = true"
 class="inline-flex items-center justify-center rounded-lg p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 lg:hidden"
 aria-label="Ouvrir le menu"
 >
 <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
 </svg>
 </button>

 <a href="{{ route($dashboardRoute) }}" class="flex items-center gap-2">
 <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
 <span class="hidden sm:inline text-sm font-semibold text-gray-900">Dashboard</span>
 </a>
 </div>

 {{-- Center: Search (optionnel) --}}
 <div class="hidden md:flex flex-1 max-w-xl">
 <div class="relative w-full">
 <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
 <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
 </svg>
 </span>
 <input
 type="text"
 placeholder="Rechercher…"
 class="w-full rounded-xl border-gray-200 bg-white pl-10 pr-4 py-2 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
 />
 </div>
 </div>

 {{-- Right: user dropdown --}}
 <div class="flex items-center gap-3">
 <span class="hidden sm:inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
 {{ ucfirst($role) }}
 </span>

 <div class="relative">
 <button
 @click="userOpen = !userOpen"
 class="group inline-flex items-center gap-3 rounded-xl px-2 py-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
 >
 <span class="hidden sm:block text-sm font-medium text-gray-800">{{ $user->name }}</span>

 <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gray-900 text-white text-sm font-semibold">
 {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
 </span>

 <svg class="h-4 w-4 text-gray-500 group-hover:text-gray-700" viewBox="0 0 20 20" fill="currentColor">
 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
 </svg>
 </button>

 <div
 x-show="userOpen"
 x-transition
 @click.outside="userOpen = false"
 class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-200 bg-white shadow-lg overflow-hidden"
 style="display:none;"
 >
 <x-dropdown-link :href="route('profile.edit')">
 {{ __('Profile') }}
 </x-dropdown-link>

 <form method="POST" action="{{ route('logout') }}">
 @csrf
 <x-dropdown-link :href="route('logout')"
 onclick="event.preventDefault(); this.closest('form').submit();">
 {{ __('Log Out') }}
 </x-dropdown-link>
 </form>
 </div>
 </div>
 </div>

 </div>
 </div>
 </header>

 {{-- Mobile overlay --}}
 <div
 x-show="sidebarOpen"
 x-transition.opacity
 class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
 @click="sidebarOpen = false"
 style="display:none;"
 ></div>

 <div class="w-full">
 <div class="flex">

 {{-- SIDEBAR --}}
 <aside
 class="fixed inset-y-0 left-0 z-50 w-72 transform bg-white border-r border-gray-200 lg:static lg:translate-x-0 transition duration-200 ease-in-out"
 :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
 aria-label="Sidebar"
 >
 {{-- Mobile top --}}
 <div class="h-16 px-4 flex items-center justify-between border-b border-gray-200 lg:hidden">
 <a href="{{ route($dashboardRoute) }}" class="flex items-center gap-2">
 <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
 <span class="text-sm font-semibold text-gray-900">Menu</span>
 </a>
 <button
 @click="sidebarOpen = false"
 class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
 aria-label="Fermer le menu"
 >
 <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
 </svg>
 </button>
 </div>

 <div class="p-4">
 {{-- Mini profile card --}}
 <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-4 shadow-sm">
 <div class="flex items-center gap-3">
 <div class="h-11 w-11 rounded-2xl bg-gray-900 text-white flex items-center justify-center font-semibold">
 {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
 </div>
 <div class="min-w-0">
 <div class="truncate text-sm font-semibold text-gray-900">{{ $user->name }}</div>
 <div class="text-xs text-gray-500">Rôle : {{ ucfirst($role) }}</div>
 </div>
 </div>
 </div>

 {{-- Nav --}}
 <nav class="mt-5 space-y-1">
 {{-- Dashboard --}}
 <a href="{{ route($dashboardRoute) }}"
 class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
 {{ $isRoute($dashboardRoute) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
 <svg class="h-5 w-5 {{ $isRoute($dashboardRoute) ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
 </svg>
 <span>{{ __('Dashboard') }}</span>
 </a>

 {{-- Emploi du Temps --}}
 <a href="{{ route('consultation.index') }}"
 class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
 {{ $isRoute('consultation.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
 <svg class="h-5 w-5 {{ $isRoute('consultation.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
 </svg>
 <span>{{ __('Emploi du Temps') }}</span>
 </a>

 {{-- Floriane --}}
 <a href="{{ route('dashboard.emploi') }}"
 class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
 {{ $isRoute('dashboard.emploi') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
 <svg class="h-5 w-5 {{ $isRoute('dashboard.emploi') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
 </svg>
 <span>{{ __('Planing') }}</span>
 </a>

 {{-- Admin only --}}
 @if($role === 'admin')
 <div class="pt-3">
 <div class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
 {{ __('Administration') }}
 </div>
 </div>

 <a href="{{ route('admin.users.index') }}"
 class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
 {{ $isRoute('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
 <svg class="h-5 w-5 {{ $isRoute('admin.users.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-4a4 4 0 10-8 0 4 4 0 008 0zM21 8a4 4 0 11-8 0 4 4 0 018 0z"/>
 </svg>
 <span>{{ __('Utilisateurs') }}</span>
 </a>

 <a href="{{ route('referential.index') }}"
 class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
 {{ $isRoute('referential.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
 <svg class="h-5 w-5 {{ $isRoute('referential.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v12a2 2 0 01-2 2z"/>
 </svg>
 <span>{{ __('Référentiel') }}</span>
 </a>
 @endif
 </nav>

 <div class="mt-6 border-t border-gray-200 pt-4">
 <form method="POST" action="{{ route('logout') }}">
 @csrf
 <button
 type="submit"
 class="w-full flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
 >
 <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
 </svg>
 <span>{{ __('Se déconnecter') }}</span>
 </button>
 </form>
 </div>
 </div>
 </aside>

 <main class="flex-1 w-full max-w-none p-8">
 <div class="w-full max-w-none">
 {{ $slot ?? '' }}
 @yield('content')
 </div>
 </main>

 </div>
 </div>
</div>