@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white dark:from-slate-950 dark:to-slate-900 transition-colors duration-300">

    {{-- 🎯 HERO SECTION --}}
    <section class="relative overflow-hidden">
        {{-- Fond décoratif subtil --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 via-transparent to-amber-50/30 dark:from-blue-950/20 dark:via-transparent dark:to-amber-950/10"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-blue-400/10 to-amber-400/10 dark:from-blue-600/5 dark:to-amber-600/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        
        <div class="relative container mx-auto px-4 py-16 md:py-24 max-w-6xl">
            <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
                
                {{-- Texte Hero --}}
                <div class="flex-1 text-center lg:text-left space-y-6">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-semibold rounded-full border border-blue-200 dark:border-blue-800">
                        ✨ Nouvelle collection disponible
                    </span>
                    
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight">
                        L'élégance au quotidien,<br>
                        <span class="text-blue-600 dark:text-blue-400">livrée chez vous</span>
                    </h1>
                    
                    <p class="text-sm md:text-base text-gray-600 dark:text-slate-400 max-w-lg mx-auto lg:mx-0 leading-relaxed">
                        Découvrez une sélection rigoureuse de produits alliant qualité, design et prix accessibles. Livraison rapide & retour gratuit.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 justify-center lg:justify-start pt-2">
                        <a href="{{ url('/produits') }}" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-200 flex items-center justify-center gap-2">
                            Découvrir la boutique
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="{{ url('/produits') }}#categories" class="w-full sm:w-auto px-6 py-2.5 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-center gap-2">
                            Explorer les catégories
                        </a>
                    </div>
                </div>

                {{-- Illustration Hero --}}
                <div class="flex-1 relative">
                    <div class="relative w-full max-w-md mx-auto">
                        <div class="aspect-square rounded-3xl bg-gradient-to-br from-blue-100 to-amber-50 dark:from-blue-900/20 dark:to-amber-900/20 border border-gray-200 dark:border-slate-800 shadow-2xl shadow-blue-500/10 flex items-center justify-center overflow-hidden">
                            <div class="text-center p-8">
                                <div class="w-24 h-24 mx-auto mb-4 bg-white dark:bg-slate-800 rounded-2xl shadow-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-slate-400 text-sm font-medium">Produits premium sélectionnés</p>
                            </div>
                        </div>
                        
                        {{-- Badge flottant --}}
                        <div class="absolute -bottom-4 -left-4 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 p-3 flex items-center gap-3 animate-bounce" style="animation-duration: 3s;">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900 dark:text-white">Livraison gratuite</p>
                                <p class="text-[10px] text-gray-500 dark:text-slate-400">Dès 50€ d'achat</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 📦 SECTION CATÉGORIES --}}
    <section id="categories" class="py-12 md:py-16 bg-white dark:bg-slate-900/50 border-y border-gray-100 dark:border-slate-800">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">Nos Catégories</h2>
                <p class="text-sm text-gray-600 dark:text-slate-400">Explorez notre univers par thématique</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach(['Électronique', 'Mode', 'Maison', 'Sport'] as $cat)
                <a href="{{ url('/produits') }}?category={{ strtolower($cat) }}" class="group p-5 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border border-gray-100 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-200 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 bg-white dark:bg-slate-700 rounded-xl shadow-sm flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="text-xl">
                            @if($loop->first) 💻 @elseif($loop->iteration == 2) 👕 @elseif($loop->iteration == 3) 🏠 @else ⚽ @endif
                        </span>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $cat }}</h3>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ⭐ SECTION AVANTAGES --}}
    <section class="py-12 md:py-16">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['icon' => '🚚', 'title' => 'Livraison Rapide', 'desc' => 'Expédition sous 24h, livraison en 2-3 jours'],
                    ['icon' => '🔒', 'title' => 'Paiement Sécurisé', 'desc' => 'Transactions cryptées via Stripe'],
                    ['icon' => '↩️', 'title' => 'Retour Gratuit', 'desc' => '30 jours pour changer d\'avis']
                ] as $adv)
                <div class="p-6 bg-white dark:bg-slate-800/50 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow text-center md:text-left">
                    <div class="text-3xl mb-3">{{ $adv['icon'] }}</div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ $adv['title'] }}</h3>
                    <p class="text-xs text-gray-600 dark:text-slate-400 leading-relaxed">{{ $adv['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 🛒 CTA SECTION --}}
    <section class="py-12 md:py-16 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
        <div class="container mx-auto px-4 max-w-4xl text-center relative">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-3">Prêt à commander ?</h2>
            <p class="text-blue-100 text-sm mb-6 max-w-lg mx-auto">Rejoignez des milliers de clients satisfaits et profitez de nos offres exclusives.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3 bg-white text-blue-700 font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 text-sm">
                    Créer un compte gratuit
                </a>
                <a href="{{ url('/produits') }}" class="w-full sm:w-auto px-8 py-3 bg-blue-700 hover:bg-blue-600 text-white font-medium rounded-xl border border-blue-500 transition-all duration-200 text-sm">
                    Voir les produits
                </a>
            </div>
        </div>
    </section>

</div>
@endsection