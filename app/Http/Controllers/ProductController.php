<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Filtre par catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Filtre par prix
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Recherche
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        $products = $query->latest()->paginate(12);
        $categories = Category::withCount('products')->get();
        
        return view('products.index', compact('products', 'categories'));
    }
    
    public function show(Category $category)
    {
        $category->load(['products' => function($query) {
            $query->latest();
        }]);
        
        return view('categories.show', compact('category'));
    }
    
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $products = Product::with('category')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(8)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => number_format($product->price, 2) . ' €',
                    'image' => $product->image ?? 'https://via.placeholder.com/100',
                    'url' => route('products.show', $product),
                    'category' => $product->category->name ?? 'Non catégorisé'
                ];
            });
        
        return response()->json(['results' => $products]);
    }
}