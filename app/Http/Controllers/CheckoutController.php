<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Votre panier est vide.');
        }

        // Calcul des totaux (côté serveur pour sécurité)
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }
        $tax = $subtotal * 0.20;
        $total = $subtotal + $tax;

        return view('checkout.index', compact('cart', 'subtotal', 'tax', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_country' => 'required|string|max:100',
            'delivery_method' => 'required|in:standard,express,relay',
        ]);

        $deliveryCosts = ['standard' => 4.90, 'express' => 9.90, 'relay' => 3.50];
        $deliveryCost = $deliveryCosts[$validated['delivery_method']];

        $cart = Session::get('cart', []);
        if (empty($cart)) abort(400, 'Panier vide.');

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        $tax = $subtotal * 0.20;
        $total = $subtotal + $tax + $deliveryCost;

        // Créer la commande
        $order = Order::create([
            'user_id' => auth()->id(),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'shipping_address' => $validated['shipping_address'],
            'shipping_city' => $validated['shipping_city'],
            'shipping_postal_code' => $validated['shipping_postal_code'],
            'shipping_country' => $validated['shipping_country'],
            'delivery_method' => $validated['delivery_method'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'delivery_cost' => $deliveryCost,
            'total' => $total,
        ]);

        // Créer les lignes de commande
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
            ]);
        }

        // Vider le panier
        Session::forget('cart');

      
    // 📧 Envoyer l'email avec facture PDF jointe
    Mail::to($validated['email'])->send(new OrderConfirmation($order));

    // Vider le panier
    Session::forget('cart');

    return redirect()->route('checkout.success', $order->id)
    ->with('success', 'Commande validée avec succès ! 🎉 Un email de confirmation a été envoyé.');

    }
}