@extends('layouts.app')

@section('content')
{{-- 🌟 HERO --}}
<section class="relative py-12 md:py-20 bg-gradient-to-br from-primary/5 via-background to-background overflow-hidden" data-aos="fade-in">
    <div class="container mx-auto px-4 text-center max-w-4xl">
        <span class="inline-block px-3 py-1 mb-4 text-xs font-semibold tracking-wide text-primary bg-primary/10 rounded-full" data-aos="fade-up" data-aos-delay="100">✨ Nouvelle collection disponible</span>
        <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold text-secondary leading-tight mb-4" data-aos="fade-up" data-aos-delay="200">
            L'élégance au quotidien, <span class="text-primary">livrée chez vous</span>
        </h1>
        <p class="text-sm md:text-base text-secondary/70 mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="300">
            Découvrez une sélection rigoureuse de produits alliant qualité, design et prix accessibles. Livraison rapide & retour gratuit.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center" data-aos="fade-up" data-aos-delay="400">
            <a href="{{ url('/produits') }}" class="btn-primary px-6 py-2.5 text-sm">Découvrir la boutique</a>
            <a href="#categories" class="px-6 py-2.5 text-sm font-medium text-secondary bg-card border border-border rounded-btn hover:border-primary/30 transition shadow-sm">Explorer les catégories</a>
        </div>
    </div>
</section>

{{-- 🛡️ TRUST BADGES --}}
<section class="py-6 md:py-8 bg-card border-y border-border" data-aos="fade-up">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['icon' => '🚚', 'title' => 'Livraison gratuite', 'desc' => 'Dès 49€ d\'achat'],
                ['icon' => '🔒', 'title' => 'Paiement sécurisé', 'desc' => 'Cryptage SSL 256-bit'],
                ['icon' => '↩️', 'title' => 'Retours simples', 'desc' => '30 jours pour changer'],
                ['icon' => '💬', 'title' => 'Support réactif', 'desc' => 'Réponse sous 24h']
            ] as $f)
            <div class="text-center p-2">
                <div class="text-xl md:text-2xl mb-1">{{ $f['icon'] }}</div>
                <h3 class="text-sm font-semibold text-secondary">{{ $f['title'] }}</h3>
                <p class="text-xs text-secondary/60 mt-0.5">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 📂 CATÉGORIES --}}
<section id="categories" class="py-10 md:py-14" data-aos="fade-up">
    <div class="container mx-auto px-4">
        <div class="text-center mb-8">
            <h2 class="text-xl md:text-2xl font-bold text-secondary mb-1">Nos Catégories</h2>
            <p class="text-sm text-secondary/60">Trouvez exactement ce qu'il vous faut</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach(['Électronique', 'Mode', 'Maison', 'Sport', 'Beauté', 'Enfants'] as $cat)
            <a href="{{ url('/produits?category=' . Str::slug($cat)) }}" class="card p-4 text-center hover:border-primary/30 transition group" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="w-10 h-10 md:w-12 md:h-12 mx-auto mb-2 md:mb-3 rounded-full bg-primary/10 flex items-center justify-center text-lg md:text-xl group-hover:scale-110 transition-transform">
                    {{ ['⚡','👗','🏠','🏃','✨','🧸'][$loop->index] }}
                </div>
                <h3 class="text-xs md:text-sm font-medium text-secondary group-hover:text-primary transition">{{ $cat }}</h3>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- 🛍️ PRODUITS POPULAIRES --}}
<section class="py-10 md:py-14 bg-background/50" data-aos="fade-up">
    <div class="container mx-auto px-4">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-8 gap-4">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-secondary mb-1">Produits Populaires</h2>
                <p class="text-sm text-secondary/60">Les coups de cœur de nos clients</p>
            </div>
            <a href="{{ url('/produits') }}" class="text-sm font-medium text-primary hover:underline flex items-center gap-1">
                Voir tout <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
            @for($i = 1; $i <= 5; $i++)
            <div class="card hover-lift" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="relative aspect-square overflow-hidden rounded-t-card bg-gray-100">
                    <img src="https://placehold.co/400x400/e2e8f0/1e293b?text=Produit+{{ $i }}" alt="Produit {{ $i }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                    @if($i % 2 == 0)
                    <span class="absolute top-2 left-2 bg-accent/90 text-white text-xs font-semibold px-2 py-0.5 rounded shadow">Promo</span>
                    @endif
                </div>
                <div class="p-3 md:p-4">
                    <span class="text-xs text-secondary/50 uppercase tracking-wide">Catégorie</span>
                    <h3 class="font-medium text-sm md:text-base text-secondary line-clamp-2 mb-1 mt-1">Nom du produit {{ $i }}</h3>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-primary font-bold text-sm md:text-base">{{ 29 + ($i * 5) }},99 €</span>
                        @if($i % 2 == 0)
                        <span class="text-secondary/50 text-xs line-through">{{ 39 + ($i * 5) }},99 €</span>
                        @endif
                    </div>
                    <button @click="addToCart(999)" class="btn-primary w-full text-xs md:text-sm py-1.5 md:py-2 relative overflow-hidden">
                        <span>Ajouter au panier</span>
                    </button>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

{{-- 📩 NEWSLETTER --}}
<section class="py-12 md:py-16 bg-primary text-white relative overflow-hidden" data-aos="fade-up">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h2 class="text-xl md:text-2xl font-bold mb-2">Restez informé de nos nouveautés</h2>
        <p class="text-sm text-white/80 mb-6 max-w-xl mx-auto">Inscrivez-vous à notre newsletter et recevez -10% sur votre première commande.</p>
        <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
            <input type="email" placeholder="votre@email.com" class="w-full px-4 py-2.5 rounded-btn text-secondary bg-white/95 focus:outline-none focus:ring-2 focus:ring-white/30 text-sm" required>
            <button type="submit" class="bg-accent hover:bg-amber-600 text-white font-medium px-5 py-2.5 rounded-btn transition shadow text-sm whitespace-nowrap">Je m'inscris</button>
        </form>
        <p class="text-xs text-white/60 mt-3">Désabonnement possible à tout moment. Pas de spam.</p>
    </div>
</section>
@endsection