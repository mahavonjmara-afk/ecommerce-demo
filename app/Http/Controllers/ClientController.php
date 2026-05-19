<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        $totalSpent = $user->orders()
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total');

        return view('client.dashboard', compact('recentOrders', 'totalSpent'));
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('client.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // 🔒 Sécurité : vérifier que la commande appartient à l'utilisateur connecté
        abort_unless($order->user_id === auth()->id(), 403);
        
        return view('client.orders.show', compact('order'));
    }

    public function invoice(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        
        // Réutilise le template PDF existant
        $pdf = Pdf::loadView('emails.invoices.pdf', ['order' => $order]);
        return $pdf->download("facture-{$order->id}.pdf");
    }
}