<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function data()
    {
        return response()->json([
            'cart' => session('cart', []),
            'totals' => $this->calculateTotals()
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1|max:99'
        ]);

        $product = Product::find($validated['product_id']);
        $cart = session('cart', []);
        $id = $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $validated['qty'];
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price_ttc,
                'qty' => $validated['qty'],
                'image' => $product->image ?? 'https://placehold.co/100x100/e2e8f0/1e293b?text=Produit'
            ];
        }

        session(['cart' => $cart]);
        return response()->json(['success' => true, 'cart' => $cart, 'totals' => $this->calculateTotals()]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'qty' => 'required|integer|min:1|max:99'
        ]);

        $cart = session('cart', []);
        $id = $validated['product_id'];

        if (isset($cart[$id])) {
            $cart[$id]['qty'] = $validated['qty'];
            if ($cart[$id]['qty'] <= 0) unset($cart[$id]);
            session(['cart' => $cart]);
        }

        return response()->json(['success' => true, 'cart' => $cart, 'totals' => $this->calculateTotals()]);
    }

    public function remove(Request $request)
    {
        $validated = $request->validate(['product_id' => 'required']);
        $cart = session('cart', []);
        unset($cart[$validated['product_id']]);
        session(['cart' => $cart]);

        return response()->json(['success' => true, 'cart' => $cart, 'totals' => $this->calculateTotals()]);
    }

    public function clear()
    {
        session()->forget('cart');
        return response()->json(['success' => true, 'cart' => [], 'totals' => $this->calculateTotals()]);
    }

    private function calculateTotals()
    {
        $cart = session('cart', []);
        $subtotal = 0; $items = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
            $items += $item['qty'];
        }

        $tax = $subtotal * 0.20; // TVA 20%
        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'items' => $items
        ];
    }
}