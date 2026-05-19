@forelse($products as $product)
<div class="card hover-lift" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 12) * 50 }}">
    <div class="relative aspect-square overflow-hidden rounded-t-card bg-gray-100 group">
        <img src="{{ $product->image ?? 'https://placehold.co/400x400/e2e8f0/1e293b?text=Produit' }}" 
             alt="{{ $product->name }}" 
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        
        @if($product->is_featured)
        <span class="absolute top-2 left-2 bg-accent/90 text-white text-xs font-semibold px-2 py-0.5 rounded shadow">Populaire</span>
        @endif
        
        @if($product->stock < 5 && $product->stock > 0)
        <span class="absolute top-2 right-2 bg-orange-500/90 text-white text-xs font-semibold px-2 py-0.5 rounded shadow">Stock limité</span>
        @endif
        
        @if($product->stock <= 0)
        <span class="absolute top-2 right-2 bg-red-500/90 text-white text-xs font-semibold px-2 py-0.5 rounded shadow">Rupture</span>
        @endif
    </div>
    
    <div class="p-3 md:p-4">
        <span class="text-xs text-secondary/50 uppercase tracking-wide">{{ $product->category?->name ?? 'Général' }}</span>
        <h3 class="font-medium text-sm md:text-base text-secondary line-clamp-2 mb-1 mt-1 hover:text-primary transition cursor-pointer">
            <a href="{{ url('/produits/' . $product->slug) }}">{{ $product->name }}</a>
        </h3>
        
        <div class="flex items-center gap-2 mb-3">
            <span class="text-primary font-bold text-sm md:text-base">{{ number_format($product->price_ttc, 2, ',', ' ') }} €</span>
            @if($product->category && $product->category->name === 'Promo')
            <span class="text-secondary/50 text-xs line-through">{{ number_format($product->price_ttc * 1.2, 2, ',', ' ') }} €</span>
            @endif
        </div>
        
        <button @click="addToCart({{ $product->id }})" 
                :disabled="{{ $product->stock <= 0 ? 'true' : 'false' }}"
                class="btn-primary w-full text-xs md:text-sm py-1.5 md:py-2 disabled:opacity-50 disabled:cursor-not-allowed">
            {{ $product->stock <= 0 ? 'Indisponible' : 'Ajouter au panier' }}
        </button>
    </div>
</div>
@empty
<div class="col-span-full text-center py-12">
    <div class="text-6xl mb-4">🔍</div>
    <h3 class="text-lg font-medium text-secondary mb-2">Aucun produit trouvé</h3>
    <p class="text-sm text-secondary/60">Essayez de modifier vos filtres ou votre recherche</p>
</div>
@endforelse