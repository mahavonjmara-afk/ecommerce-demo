<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer toutes les catégories pour les filtres
        $categories = Category::where('is_active', true)->get();
        
        // Requête de base
        $query = Product::with('category')->where('is_active', true);

        // 🔍 Filtre de recherche textuelle
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function($cat) use ($search) {
                      $cat->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // 🏷️ Filtre par catégorie
        if ($category = $request->get('category')) {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        // 💰 Filtre par prix min
        if ($minPrice = $request->get('min_price')) {
            $query->where('price_ttc', '>=', $minPrice);
        }

        // 💰 Filtre par prix max
        if ($maxPrice = $request->get('max_price')) {
            $query->where('price_ttc', '<=', $maxPrice);
        }

        // 🔄 Tri
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price_ttc', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_ttc', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default: // latest
                $query->latest();
        }

        $products = $query->paginate(12);

        // Si requête AJAX, retourner uniquement le HTML des produits
        if ($request->ajax()) {
            return response()->json([
                'html' => view('products.partials.product-grid', compact('products'))->render(),
                'count' => $products->total(),
                'pagination' => $products->links()->toHtml(),
            ]);
        }

        return view('products.index', compact('products', 'categories'));
    }

    // Endpoint API pour la recherche instantanée (optionnel)
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $products = Product::with('category')
            ->where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(8)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => number_format($product->price_ttc, 2, ',', ' ') . ' €',
                    'image' => $product->image ?? 'https://placehold.co/100x100/e2e8f0/1e293b?text=Produit',
                    'url' => url('/produits/' . $product->slug),
                ];
            });

        return response()->json(['results' => $products]);
    }
}