@extends('layouts.app')

@section('title', 'Nos Produits')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-300">
    
    {{-- Header de la page --}}
    <div class="bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">Nos Produits</h1>
                    <p class="text-sm text-gray-600 dark:text-slate-400">{{ $products->total() }} articles disponibles</p>
                </div>
                
                {{-- Filtres rapides --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0">
                    <button onclick="filterPrice('all')" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg whitespace-nowrap">Tous les prix</button>
                    <button onclick="filterPrice('0-50')" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 whitespace-nowrap">Moins de 50€</button>
                    <button onclick="filterPrice('50-100')" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 whitespace-nowrap">50€ - 100€</button>
                    <button onclick="filterPrice('100+')" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 whitespace-nowrap">Plus de 100€</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-6">
            
            {{-- Sidebar Filtres --}}
            <aside class="w-full lg:w-64 shrink-0">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 p-5 sticky top-24">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filtres
                    </h3>
                    
                    {{-- Catégories --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-3">Catégories</h4>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" value="{{ $category->id }}" class="category-filter w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="applyFilters()">
                                <span class="text-sm text-gray-600 dark:text-slate-400 group-hover:text-gray-900 dark:group-hover:text-white transition">{{ $category->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Prix --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-3">Prix</h4>
                        <div class="flex items-center gap-2">
                            <input type="number" id="minPrice" placeholder="Min" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm" onchange="applyFilters()">
                            <span class="text-gray-400">-</span>
                            <input type="number" id="maxPrice" placeholder="Max" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm" onchange="applyFilters()">
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Grille Produits --}}
            <main class="flex-1">
                {{-- Barre de recherche inline --}}
                <div class="mb-6 relative">
                    <input type="text" 
                           x-data 
                           @input.debounce.300ms="$dispatch('search', $event.target.value)"
                           placeholder="Rechercher un produit..." 
                           class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600">
                    <svg class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                {{-- Résultats --}}
                <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($products as $product)
                    <div class="product-card group bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 overflow-hidden hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300" 
                         data-category="{{ $product->category_id }}"
                         data-price="{{ $product->price }}">
                        
                        {{-- Image --}}
                        <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-slate-800">
                            <img src="{{ $product->image ?? 'https://via.placeholder.com/400' }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            
                            {{-- Badge promo --}}
                            @if($product->discount)
                            <span class="absolute top-3 left-3 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">-{{ $product->discount }}%</span>
                            @endif
                            
                            {{-- Bouton ajout rapide --}}
                            <button onclick="addToCart({{ $product->id }})" 
                                    class="absolute bottom-3 right-3 w-10 h-10 bg-white dark:bg-slate-800 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-200 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </button>
                        </div>
                        
                        {{-- Infos --}}
                        <div class="p-4">
                            <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mb-1">{{ $product->category->name ?? 'Non catégorisé' }}</p>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-slate-400 line-clamp-2 mb-3">{{ Str::limit($product->description, 80) }}</p>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    @if($product->discount)
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($product->price * (1 - $product->discount/100), 2) }}€</span>
                                    <span class="text-sm text-gray-400 line-through ml-2">{{ number_format($product->price, 2) }}€</span>
                                    @else
                                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($product->price, 2) }}€</span>
                                    @endif
                                </div>
                                <button onclick="addToCart({{ $product->id }})" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                    Ajouter
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <p class="text-gray-600 dark:text-slate-400">Aucun produit trouvé</p>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </main>
        </div>
    </div>
</div>

<script>
// Filtrage client-side
function applyFilters() {
    const categories = Array.from(document.querySelectorAll('.category-filter:checked')).map(cb => cb.value);
    const minPrice = document.getElementById('minPrice').value;
    const maxPrice = document.getElementById('maxPrice').value;
    const cards = document.querySelectorAll('.product-card');
    
    cards.forEach(card => {
        const category = parseInt(card.dataset.category);
        const price = parseFloat(card.dataset.price);
        
        const matchCategory = categories.length === 0 || categories.includes(category);
        const matchPrice = (!minPrice || price >= minPrice) && (!maxPrice || price <= maxPrice);
        
        card.style.display = matchCategory && matchPrice ? 'block' : 'none';
    });
}

function filterPrice(range) {
    document.getElementById('minPrice').value = '';
    document.getElementById('maxPrice').value = '';
    
    if (range === '0-50') {
        document.getElementById('minPrice').value = 0;
        document.getElementById('maxPrice').value = 50;
    } else if (range === '50-100') {
        document.getElementById('minPrice').value = 50;
        document.getElementById('maxPrice').value = 100;
    } else if (range === '100+') {
        document.getElementById('minPrice').value = 100;
    }
    
    applyFilters();
}

function addToCart(productId) {
    // Utiliser Alpine.js pour ajouter au panier
    window.dispatchEvent(new CustomEvent('cart:add', { detail: { productId, qty: 1 } }));
}

// Recherche instantanée
document.addEventListener('input', function(e) {
    if (e.target.placeholder === 'Rechercher un produit...') {
        const search = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            const name = card.querySelector('h3').textContent.toLowerCase();
            const desc = card.querySelector('p.line-clamp-2').textContent.toLowerCase();
            const match = name.includes(search) || desc.includes(search);
            card.style.display = match ? 'block' : 'none';
        });
    }
});
</script>
@endsection