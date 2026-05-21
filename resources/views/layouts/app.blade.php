<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'E-Shop' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- 🌓 Prévention du FOUC (Flash of Unstyled Content) --}}
    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && systemPrefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <style>
        body { transition: opacity 0.4s ease-out, background-color 0.3s ease, color 0.3s ease; }
        @keyframes pageFadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        main > *, .container, .card, header, footer, [data-aos], [x-data] { animation: pageFadeIn 0.5s ease-out forwards; }
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.15s; }
        .card:nth-child(3) { animation-delay: 0.2s; }
        .page-loader { position: fixed; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #F4D03F 0%, #D4AF37 50%, #F4D03F 100%); background-size: 200% 100%; z-index: 9999; animation: shimmer 1.5s infinite; display: none; }
        .page-loader.active { display: block; }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(245,158,11,0.5); }
        @keyframes slide-in-right { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .animate-slide-in-right { animation: slide-in-right 0.3s ease-out; }
    </style>
</head>

<body x-data="{ ...ecommerce(), ...theme() }" class="min-h-screen flex flex-col bg-background text-secondary font-sans antialiased dark:bg-slate-950 dark:text-slate-200 transition-colors duration-300">

    {{-- 🎬 Barre de progression de chargement --}}
    <div x-show="pageLoading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="page-loader" :class="{ 'active': pageLoading }"></div>

    {{-- 🔝 HEADER PREMIUM --}}
    <header x-data="{ mobileOpen: false }" class="bg-[#1E40AF] dark:bg-slate-900 text-white dark:text-slate-200 border-b border-amber-500/30 dark:border-slate-700 backdrop-blur-md sticky top-0 z-50 shadow-lg transition-colors duration-300">
        <nav class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ url('/') }}" @click="navigateTo($event, '{{ url('/') }}')" class="text-xl md:text-2xl font-bold text-white dark:text-slate-100 tracking-wide hover:text-amber-400 transition shrink-0">E-Shop<span class="text-amber-400">.</span></a>

            {{-- Recherche Desktop --}}
            <div x-data="globalSearch()" class="hidden md:block relative mx-4 flex-1 max-w-lg" @click.away="open = false">
                <div class="relative">
                    <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="open = true" placeholder="Rechercher un produit..." class="w-full pl-10 pr-4 py-2 bg-white/10 dark:bg-slate-800 border border-white/20 dark:border-slate-600 rounded-lg text-sm text-white dark:text-slate-200 placeholder-white/60 dark:placeholder-slate-400 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition-all backdrop-blur-sm">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-white/70 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div x-show="open && results.length > 0" x-transition class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl z-50 overflow-hidden max-h-60 overflow-y-auto">
                    <template x-for="product in results" :key="product.id">
                        <a :href="product.url" @click="navigateTo($event, product.url)" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 transition border-b border-gray-100 dark:border-slate-700 last:border-0">
                            <img :src="product.image" class="w-10 h-10 object-cover rounded bg-gray-100 dark:bg-slate-700">
                            <div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 dark:text-slate-100 truncate" x-text="product.name"></p><p class="text-xs text-[#1E40AF] dark:text-amber-400 font-semibold" x-text="product.price"></p></div>
                        </a>
                    </template>
                </div>
            </div>

            {{-- Menu Desktop --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ url('/produits') }}" @click="navigateTo($event, '{{ url('/produits') }}')" class="text-sm font-medium hover:text-amber-400 transition-colors">Produits</a>
                <a href="{{ url('/produits') }}#categories" @click="navigateTo($event, '{{ url('/produits') }}#categories')" class="text-sm font-medium hover:text-amber-400 transition-colors">Catégories</a>

                {{-- 🌓 Toggle Theme --}}
                <button @click="toggle()" class="p-2 rounded-lg hover:bg-white/10 dark:hover:bg-slate-700 transition text-white/80 hover:text-white" aria-label="Mode sombre" title="Basculer le mode clair/sombre"><span x-html="icon"></span></button>

                {{-- Panier --}}
                <button @click.prevent="isOpen = true" class="relative flex items-center gap-2 text-sm font-medium hover:text-amber-400 transition-colors group">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="hidden lg:inline">Panier</span>
                    <span class="absolute -top-2 -right-2 bg-amber-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-md" x-text="totals.items">0</span>
                </button>

                {{-- Auth --}}
                @auth
                    @if(auth()->user()->is_admin)<a href="{{ url('/admin/commandes') }}" @click="navigateTo($event, '{{ url('/admin/commandes') }}')" class="text-sm font-medium text-rose-300 hover:text-rose-200 transition">Admin</a>@endif
                    <a href="{{ route('client.dashboard') }}" @click="navigateTo($event, '{{ route('client.dashboard') }}')" class="flex items-center gap-2 text-sm font-medium hover:text-amber-400 transition-colors group"><svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Mon compte</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button type="submit" class="flex items-center gap-2 text-sm font-medium text-white/80 hover:text-rose-300 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Déconnexion</button></form>
                @else
                    <a href="{{ route('login') }}" @click="navigateTo($event, '{{ route('login') }}')" class="flex items-center gap-2 text-sm font-medium hover:text-amber-400 transition-colors group"><svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>Connexion</a>
                    <a href="{{ route('register') }}" @click="navigateTo($event, '{{ route('register') }}')" class="flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-white text-xs font-semibold px-4 py-1.5 rounded-lg transition-all shadow-md hover:shadow-lg">S'inscrire</a>
                @endauth
            </div>

            {{-- Mobile Toggle --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-amber-400 hover:text-amber-300 transition"><svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg><svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </nav>

            {{-- Menu Mobile --}}
        <div x-show="mobileOpen" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 -translate-y-2" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100 translate-y-0" 
             x-transition:leave-end="opacity-0 -translate-y-2" 
             x-cloak 
             class="md:hidden bg-[#1E40AF] dark:bg-slate-900 border-t border-white/20 dark:border-slate-700 px-4 py-3 space-y-3">
            
            <div class="mb-3">
                <input type="text" placeholder="Rechercher..." class="w-full px-4 py-2 bg-white/10 dark:bg-slate-800 border border-white/20 dark:border-slate-600 rounded-lg text-sm text-white dark:text-slate-200 placeholder-white/60 dark:placeholder-slate-400 focus:outline-none focus:border-amber-400">
            </div>

            <a href="{{ url('/produits') }}" @click="navigateTo($event, '{{ url('/produits') }}'); mobileOpen = false" class="block text-sm py-2 hover:text-amber-400 transition">Produits</a>
            <a href="{{ url('/produits') }}#categories" @click="navigateTo($event, '{{ url('/produits') }}#categories'); mobileOpen = false" class="block text-sm py-2 hover:text-amber-400 transition">Catégories</a>
            
            {{-- 🌓 Toggle Mode Sombre/Clair (Ajouté pour mobile) --}}
            <button @click="toggle(); mobileOpen = false" class="w-full text-left text-sm py-2 flex items-center justify-between text-white/80 hover:text-white transition">
                <span>Mode sombre / clair</span>
                <span x-html="icon" class="ml-2"></span>
            </button>

            <button @click.prevent="isOpen = true; mobileOpen = false" class="w-full text-left text-sm py-2 hover:text-amber-400 flex items-center justify-between">
                <span>Panier</span>
                <span class="bg-amber-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" x-text="totals.items">0</span>
            </button>
            
            @auth
                @if(auth()->user()->is_admin)<a href="{{ url('/admin/commandes') }}" @click="navigateTo($event, '{{ url('/admin/commandes') }}'); mobileOpen = false" class="block text-sm py-2 text-rose-300 hover:text-rose-200">Admin Panel</a>@endif
                <a href="{{ route('client.dashboard') }}" @click="navigateTo($event, '{{ route('client.dashboard') }}'); mobileOpen = false" class="block text-sm py-2 hover:text-amber-400">Mon compte</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full text-left text-sm py-2 text-rose-300 hover:text-rose-200">Déconnexion</button></form>
            @else
                <a href="{{ route('login') }}" @click="navigateTo($event, '{{ route('login') }}'); mobileOpen = false" class="block text-sm py-2 text-amber-400 font-semibold">Connexion</a>
                <a href="{{ route('register') }}" @click="navigateTo($event, '{{ route('register') }}'); mobileOpen = false" class="block text-sm py-2 text-amber-400 font-semibold">S'inscrire</a>
            @endauth
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))<div class="bg-success/10 dark:bg-success/20 border-l-4 border-success text-success dark:text-success/90 px-4 py-3 text-sm" role="alert">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-danger/10 dark:bg-danger/20 border-l-4 border-danger text-danger dark:text-danger/90 px-4 py-3 text-sm" role="alert">{{ session('error') }}</div>@endif

    {{-- Main Content --}}
    <main class="flex-1">@yield('content')</main>

    {{-- Footer --}}
    <footer class="bg-[#0A0A0A] dark:bg-slate-950 text-gray-300 dark:text-slate-400 border-t border-amber-500/20 dark:border-slate-800 transition-colors duration-300">
        <div class="container mx-auto px-4 py-12 md:py-16">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-12 pb-8 border-b border-gray-800 dark:border-slate-800">
                <div class="text-center md:text-left"><h2 class="text-2xl md:text-3xl font-bold text-amber-400 tracking-tight">E-Shop</h2><p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Votre boutique en ligne d'exception</p></div>
                <span class="px-4 py-1.5 rounded-full border border-amber-500/30 text-xs font-medium text-amber-400 bg-amber-500/5">✨ Service Client Premium</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 mb-12">
                <div class="space-y-4"><h3 class="text-lg font-semibold text-white dark:text-slate-200">À propos</h3><p class="text-sm text-gray-400 dark:text-slate-400 leading-relaxed">Découvrez une sélection rigoureuse de produits alliant qualité, design et prix accessibles.</p></div>
                <div class="space-y-4"><h3 class="text-lg font-semibold text-white dark:text-slate-200">Navigation</h3><ul class="space-y-3 text-sm"><li><a href="{{ url('/') }}" @click="navigateTo($event, '{{ url('/') }}')" class="hover:text-amber-400 transition-colors duration-300 flex items-center gap-2 group"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full group-hover:scale-125 transition-transform"></span>Accueil</a></li><li><a href="{{ url('/produits') }}" @click="navigateTo($event, '{{ url('/produits') }}')" class="hover:text-amber-400 transition-colors duration-300 flex items-center gap-2 group"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full group-hover:scale-125 transition-transform"></span>Boutique</a></li><li><a href="{{ route('client.dashboard') }}" @click="navigateTo($event, '{{ route('client.dashboard') }}')" class="hover:text-amber-400 transition-colors duration-300 flex items-center gap-2 group"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full group-hover:scale-125 transition-transform"></span>Mon Compte</a></li></ul></div>
                <div class="space-y-4"><h3 class="text-lg font-semibold text-white dark:text-slate-200">Contact</h3><ul class="space-y-3 text-sm text-gray-400 dark:text-slate-400"><li class="flex items-start gap-3"><svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>301 Rue du Commerce, 75015 Madagascar</span></li><li class="flex items-center gap-3"><svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><a href="mailto:contact@eshop.test" class="hover:text-amber-400 transition">contact@eshop.test</a></li></ul></div>
                <div class="space-y-4"><h3 class="text-lg font-semibold text-white dark:text-slate-200">Suivez-nous</h3><div class="grid grid-cols-3 gap-3"><a href="https://facebook.com/VotrePage" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-gray-900/50 dark:bg-slate-800 border border-gray-800 dark:border-slate-700 hover:border-amber-500/50 hover:bg-amber-500/10 transition-all duration-300 hover:-translate-y-1"><svg class="w-6 h-6 text-gray-400 group-hover:text-amber-400 transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg><span class="text-[10px] text-gray-500 dark:text-slate-500 mt-1.5 group-hover:text-amber-300 font-medium">Facebook</span></a><a href="https://instagram.com/VotreCompte" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-gray-900/50 dark:bg-slate-800 border border-gray-800 dark:border-slate-700 hover:border-amber-500/50 hover:bg-amber-500/10 transition-all duration-300 hover:-translate-y-1"><svg class="w-6 h-6 text-gray-400 group-hover:text-amber-400 transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg><span class="text-[10px] text-gray-500 dark:text-slate-500 mt-1.5 group-hover:text-amber-300 font-medium">Instagram</span></a><a href="https://twitter.com/VotreCompte" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-gray-900/50 dark:bg-slate-800 border border-gray-800 dark:border-slate-700 hover:border-amber-500/50 hover:bg-amber-500/10 transition-all duration-300 hover:-translate-y-1"><svg class="w-6 h-6 text-gray-400 group-hover:text-amber-400 transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg><span class="text-[10px] text-gray-500 dark:text-slate-500 mt-1.5 group-hover:text-amber-300 font-medium">Twitter</span></a><a href="https://linkedin.com/company/VotrePage" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-gray-900/50 dark:bg-slate-800 border border-gray-800 dark:border-slate-700 hover:border-amber-500/50 hover:bg-amber-500/10 transition-all duration-300 hover:-translate-y-1"><svg class="w-6 h-6 text-gray-400 group-hover:text-amber-400 transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg><span class="text-[10px] text-gray-500 dark:text-slate-500 mt-1.5 group-hover:text-amber-300 font-medium">LinkedIn</span></a><a href="https://wa.me/33612345678" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-gray-900/50 dark:bg-slate-800 border border-gray-800 dark:border-slate-700 hover:border-amber-500/50 hover:bg-amber-500/10 transition-all duration-300 hover:-translate-y-1"><svg class="w-6 h-6 text-gray-400 group-hover:text-amber-400 transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg><span class="text-[10px] text-gray-500 dark:text-slate-500 mt-1.5 group-hover:text-amber-300 font-medium">WhatsApp</span></a><a href="tel:+33123456789" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-gray-900/50 dark:bg-slate-800 border border-gray-800 dark:border-slate-700 hover:border-amber-500/50 hover:bg-amber-500/10 transition-all duration-300 hover:-translate-y-1"><svg class="w-6 h-6 text-gray-400 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg><span class="text-[10px] text-gray-500 dark:text-slate-500 mt-1.5 group-hover:text-amber-300 font-medium">Appeler</span></a></div></div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 pt-8 border-t border-gray-800 dark:border-slate-800 text-xs text-gray-500 dark:text-slate-500"><p>&copy; {{ date('Y') }} E-Shop. Tous droits réservés.</p><div class="flex gap-6"><a href="/cgv" class="hover:text-amber-400 transition">CGV</a><a href="/confidentialite" class="hover:text-amber-400 transition">Confidentialité</a><a href="/mentions" class="hover:text-amber-400 transition">Mentions légales</a></div></div>
        </div>
    </footer>

    {{-- 🛒 PANIER MODAL - UNIQUE ET CORRIGÉ --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95" 
         class="fixed inset-0 z-[9999] flex items-start justify-center p-4 pt-24 pointer-events-none" 
         style="display: none;">
        
        {{-- Overlay --}}
        <div @click="isOpen = false" 
             x-show="isOpen" 
             class="absolute inset-0 bg-black/60 backdrop-blur-sm pointer-events-auto"></div>
        
        {{-- Carte Modale --}}
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden pointer-events-auto flex flex-col max-h-[85vh] z-[10000]">
            
            {{-- HEADER --}}
            <div class="px-6 py-4 bg-blue-600 text-white flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <div>
                        <h2 class="text-lg font-bold leading-none">Votre Panier</h2>
                        <p class="text-xs text-blue-100 mt-1 opacity-90"><span x-text="totals.items">0</span> article(s)</p>
                    </div>
                </div>
                <button @click="isOpen = false" class="p-2 hover:bg-blue-700 rounded-lg transition text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            {{-- CONTENU --}}
            <div class="flex-1 overflow-y-auto p-6 bg-gray-100 min-h-[300px] flex flex-col justify-center">
                
                {{-- Panier vide --}}
                <template x-if="Object.keys(items).length === 0">
                    <div class="text-center py-8">
                        <div class="w-20 h-20 mx-auto bg-gray-200 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-6">Votre panier est vide</h3>
                        <a href="{{ url('/produits') }}" @click="isOpen = false" class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm">
                            Voir les produits
                        </a>
                    </div>
                </template>
                
                {{-- Liste articles --}}
                <div class="space-y-3" x-show="Object.keys(items).length > 0">
                    <template x-for="(item, id) in items" :key="id">
                        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 flex gap-4 items-center">
                            <img :src="item.image" class="w-16 h-16 object-cover rounded bg-gray-100">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate" x-text="item.name"></h4>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="flex items-center border border-gray-300 rounded bg-white">
                                        <button @click="updateQty(id, parseInt(item.qty) - 1)" class="px-2 py-1 text-xs hover:bg-gray-100 text-gray-600" :disabled="item.qty <= 1">−</button>
                                        <span class="px-2 text-xs font-bold text-gray-900" x-text="item.qty"></span>
                                        <button @click="updateQty(id, parseInt(item.qty) + 1)" class="px-2 py-1 text-xs hover:bg-gray-100 text-gray-600">+</button>
                                    </div>
                                    <span class="text-sm font-bold text-blue-600" x-text="formatPrice(item.price * item.qty)"></span>
                                </div>
                            </div>
                            <button @click="removeItem(id)" class="text-gray-400 hover:text-red-500 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            
            {{-- FOOTER / Totaux --}}
            <div class="p-5 bg-white border-t border-gray-200 space-y-4" x-show="totals.items > 0">
                <div class="flex justify-between items-center text-lg font-bold text-gray-900">
                    <span>Total TTC</span>
                    <span class="text-blue-600" x-text="formatPrice(totals.total)"></span>
                </div>
                <a href="{{ url('/checkout') }}" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition text-center block shadow-sm">
                    Passer la commande
                </a>
            </div>
        </div>
    </div>

    {{-- Notifications Toasts --}}
    @auth
    <div x-data="{ toasts: [] }" x-init="if (window.Echo) { window.Echo.private('user.{{ auth()->id() }}').listen('.OrderStatusUpdated', (e) => { this.toasts.unshift({ id: Date.now(), message: e.message, status: e.status, orderId: e.order_id }); setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== this.toasts[0]?.id); }, 5000); }); }" class="fixed bottom-4 right-4 z-[70] space-y-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id"><div class="pointer-events-auto bg-card dark:bg-slate-800 border border-border dark:border-slate-700 shadow-lg rounded-lg p-4 max-w-sm animate-slide-in-right"><div class="flex items-start gap-3"><span class="text-xl" x-text="toast.status === 'delivered' ? '🏠' : (toast.status === 'shipped' ? '🚚' : '📦')"></span><div class="flex-1"><p class="text-sm font-medium text-secondary dark:text-slate-200" x-text="toast.message"></p><a :href="'/mon-compte/commande/' + toast.orderId" class="text-xs text-primary dark:text-amber-400 hover:underline mt-1 inline-block">Voir le suivi →</a></div><button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-secondary/40 dark:text-slate-500 hover:text-danger">✕</button></div></div></template>
    </div>
    @endauth

    @stack('scripts')
</body>
</html>