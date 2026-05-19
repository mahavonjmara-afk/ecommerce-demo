@extends('layouts.app')

@section('content')
<div class="py-8 md:py-12 bg-background/50 min-h-screen">
    <div class="container mx-auto px-4 max-w-7xl">
        {{-- En-tête --}}
        <div class="mb-8" data-aos="fade-up">
            <h1 class="text-2xl md:text-3xl font-bold text-secondary mb-2">Nos Produits</h1>
            <p class="text-sm text-secondary/60">{{ $products->total() }} produit(s) disponible(s)</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- 🔍 SIDEBAR FILTRES --}}
            <aside class="lg:col-span-1" x-data="productFilters()" x-init="init()" data-aos="fade-right">
                <div class="card p-5 sticky top-24 space-y-6">
                    {{-- Recherche textuelle --}}
                    <div>
                        <label class="input-label flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Recherche
                        </label>
                        <input type="text" 
                               x-model="filters.search" 
                               @input.debounce.300ms="applyFilters()"
                               placeholder="Nom, description..." 
                               class="input-field text-sm">
                    </div>

                    {{-- Catégories --}}
                    <div>
                        <label class="input-label">Catégories</label>
                        <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded transition">
                                <input type="radio" name="category" value="" x-model="filters.category" @change="applyFilters()" class="rounded border-border text-primary focus:ring-primaryLight">
                                <span class="text-sm text-secondary">Toutes</span>
                            </label>
                            @foreach($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded transition">
                                <input type="radio" name="category" value="{{ $cat->slug }}" x-model="filters.category" @change="applyFilters()" class="rounded border-border text-primary focus:ring-primaryLight">
                                <span class="text-sm text-secondary">{{ $cat->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Prix --}}
                    <div>
                        <label class="input-label">Prix</label>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <input type="number" x-model="filters.minPrice" @input.debounce.500ms="applyFilters()" placeholder="Min" class="input-field text-sm">
                            <input type="number" x-model="filters.maxPrice" @input.debounce.500ms="applyFilters()" placeholder="Max" class="input-field text-sm">
                        </div>
                        <input type="range" min="0" max="500" step="10" 
                               x-model="filters.maxPrice" 
                               @input.debounce.300ms="applyFilters()"
                               class="w-full accent-primary">
                        <div class="flex justify-between text-xs text-secondary/60 mt-1">
                            <span>0€</span>
                            <span>500€+</span>
                        </div>
                    </div>

                    {{-- Tri --}}
                    <div>
                        <label class="input-label">Trier par</label>
                        <select x-model="filters.sort" @change="applyFilters()" class="input-field text-sm">
                            <option value="latest">Plus récents</option>
                            <option value="price_asc">Prix croissant</option>
                            <option value="price_desc">Prix décroissant</option>
                            <option value="name_asc">Nom A-Z</option>
                            <option value="name_desc">Nom Z-A</option>
                        </select>
                    </div>

                    {{-- Reset --}}
                    <button @click="resetFilters()" class="w-full py-2 text-sm text-secondary/70 hover:text-primary border border-border hover:border-primary/30 rounded-btn transition">
                        Réinitialiser les filtres
                    </button>

                    {{-- Résultats --}}
                    <div class="pt-4 border-t border-border">
                        <p class="text-xs text-center text-secondary/60">
                            <span x-text="productCount"></span> produit(s) trouvé(s)
                        </p>
                    </div>
                </div>
            </aside>

            {{-- 📦 GRILLE PRODUITS --}}
            <main class="lg:col-span-3">
                <div x-data="productGrid()" x-init="init()" class="space-y-6">
                    {{-- Loading --}}
                    <div x-show="loading" class="flex justify-center items-center py-12">
                        <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    {{-- Grille --}}
                    <div id="product-grid" 
                         x-html="gridHtml"
                         class="grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4"
                         data-aos="fade-up">
                    </div>

                    {{-- Pagination --}}
                    <div id="pagination" x-html="paginationHtml" class="flex justify-center"></div>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
function productFilters() {
    return {
        filters: {
            search: '{{ request('search') }}',
            category: '{{ request('category') }}',
            minPrice: '{{ request('min_price') }}',
            maxPrice: '{{ request('max_price') }}',
            sort: '{{ request('sort', 'latest') }}'
        },
        productCount: {{ $products->total() }},
        
        init() {
            // Écouter les événements de mise à jour
            window.addEventListener('filters-updated', (event) => {
                this.productCount = event.detail.count;
            });
        },
        
        applyFilters() {
            const url = new URL(window.location.href);
            Object.keys(this.filters).forEach(key => {
                if (this.filters[key]) {
                    url.searchParams.set(key, this.filters[key]);
                } else {
                    url.searchParams.delete(key);
                }
            });
            
            // Naviguer vers la nouvelle URL (Alpine gère le rechargement)
            window.history.pushState({}, '', url);
            window.dispatchEvent(new CustomEvent('filters-changed', { detail: { url: url.toString() } }));
        },
        
        resetFilters() {
            this.filters = { search: '', category: '', minPrice: '', maxPrice: '', sort: 'latest' };
            this.applyFilters();
        }
    }
}

function productGrid() {
    return {
        loading: false,
        gridHtml: @js(view('products.partials.product-grid', ['products' => $products])->render()),
        paginationHtml: @js($products->links()->toHtml()),
        
        init() {
            window.addEventListener('filters-changed', async (event) => {
                this.loading = true;
                try {
                    const response = await fetch(event.detail.url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    this.gridHtml = data.html;
                    this.paginationHtml = data.pagination;
                    window.dispatchEvent(new CustomEvent('filters-updated', { detail: data }));
                } catch (error) {
                    console.error('Error loading products:', error);
                } finally {
                    this.loading = false;
                }
            });
        }
    }
}
</script>
@endsection