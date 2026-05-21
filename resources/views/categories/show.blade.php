@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-300">
    
    {{-- Hero Catégorie --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl">
                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium mb-3">
                    {{ $category->products_count ?? $category->products()->count() }} produits
                </span>
                <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $category->name }}</h1>
                <p class="text-blue-100 text-sm md:text-base">{{ $category->description ?? 'Découvrez tous nos produits dans cette catégorie.' }}</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        {{-- Recherche instantanée dans la catégorie --}}
        <div class="mb-8 relative max-w-xl">
            <input type="text" 
                   x-data 
                   @input.debounce.300ms="$dispatch('search', $event.target.value)"
                   placeholder="Rechercher dans cette catégorie..." 
                   class="w-full px-5 py-3.5 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 shadow-sm">
            <svg class="w-5 h-5 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        {{-- Grille Produits --}}
        <div id="categoryProducts" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($category->products as $product)
            <div class="product-card group bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 overflow-hidden hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300" 
                 data-name="{{ strtolower($product->name) }}"
                 data-desc="{{ strtolower($product->description) }}">
                
                {{-- Image --}}
                <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-slate-800">
                    <img src="{{ $product->image ?? 'https://via.placeholder.com/400' }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    
                    @if($product->discount)
                    <span class="absolute top-3 left-3 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">-{{ $product->discount }}%</span>
                    @endif
                    
                    <button onclick="addToCart({{ $product->id }})" 
                            class="absolute bottom-3 right-3 w-10 h-10 bg-white dark:bg-slate-800 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-200 hover:bg-blue-600 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </button>
                </div>
                
                {{-- Infos --}}
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-slate-400 line-clamp-2 mb-3">{{ Str::limit($product->description, 60) }}</p>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            @if($product->discount)
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($product->price * (1 - $product->discount/100), 2) }}€</span>
                            <span class="text-sm text-gray-400 line-through ml-2">{{ number_format($product->price, 2) }}€</span>
                            @else
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($product->price, 2) }}€</span>
                            @endif
                        </div>
                        <button onclick="addToCart({{ $product->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition">
                            Ajouter
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16">
                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <p class="text-gray-600 dark:text-slate-400 mb-2">Aucun produit dans cette catégorie</p>
                <a href="{{ url('/produits') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Voir tous les produits →</a>
            </div>
            @endforelse
        </div>

        {{-- Retour --}}
        <div class="mt-12 text-center">
            <a href="{{ url('/produits') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour à tous les produits
            </a>
        </div>
    </div>
</div>

<script>
// Recherche instantanée dans la catégorie
document.addEventListener('input', function(e) {
    if (e.target.placeholder.includes('Rechercher dans cette catégorie')) {
        const search = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            const name = card.dataset.name;
            const desc = card.dataset.desc;
            const match = name.includes(search) || desc.includes(search);
            card.style.display = match ? 'block' : 'none';
        });
    }
});

function addToCart(productId) {
    window.dispatchEvent(new CustomEvent('cart:add', { detail: { productId, qty: 1 } }));
}
</script>
@endsection