<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Page Produits avec Filtres & Recherche
Route::get('/produits', [ProductController::class, 'index'])->name('products.index');

// API Recherche Instantanée (Header)
Route::get('/api/products/search', [ProductController::class, 'search'])->name('api.products.search');

/*
|--------------------------------------------------------------------------
| Routes Panier (Guest & Auth)
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/data', [CartController::class, 'data'])->name('data');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

/*
|--------------------------------------------------------------------------
| Routes Checkout (Guest & Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('web')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    
    Route::get('/commande/{order}/merci', function ($orderId) {
        return view('checkout.success', ['orderId' => $orderId]);
    })->name('checkout.success');
});

/*
|--------------------------------------------------------------------------
| Routes Paiement (Stripe) - Requiert Auth
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('paiement')->name('payment.')->group(function () {
    Route::get('/commande/{order}', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
});

// Webhook Stripe (Public, gère la signature de sécurité)
Route::post('/webhook/stripe', [PaymentController::class, 'webhook'])->name('webhook.stripe');

/*
|--------------------------------------------------------------------------
| Routes Espace Client - Requiert Auth
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('mon-compte')->name('client.')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('dashboard');
    Route::get('/commandes', [ClientController::class, 'orders'])->name('orders.index');
    Route::get('/commande/{order}', [ClientController::class, 'show'])->name('orders.show');
    Route::get('/commande/{order}/facture', [ClientController::class, 'invoice'])->name('orders.invoice');
});

/*
|--------------------------------------------------------------------------
| Routes Admin - Requiert Auth + Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin Principal
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Gestion Commandes
    Route::get('/commandes', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/commandes/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('/commandes/export/csv', [OrderController::class, 'exportCsv'])->name('orders.export.csv');
    Route::get('/commandes/export/pdf', [OrderController::class, 'exportPdf'])->name('orders.export.pdf');
});

/*
|--------------------------------------------------------------------------
| Routes Authentification & Profil (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🔥 REDIRECTION INTELLIGENTE POST-CONNEXION
    Route::get('/dashboard', function () {
        // Si Admin → Redirige vers /admin
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        // Si Client → Redirige vers /mon-compte
        return redirect()->route('client.dashboard');
    })->name('dashboard');
});

require __DIR__.'/auth.php';