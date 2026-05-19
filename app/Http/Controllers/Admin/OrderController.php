<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items')->latest();

        if ($status = $request->query('status')) $query->where('status', $status);
        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('shipping_city', 'like', "%{$search}%");
            });
        }
        if ($dateFrom = $request->query('date_from')) $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo = $request->query('date_to')) $query->whereDate('created_at', '<=', $dateTo);

        $orders = $query->paginate(15)->appends($request->query());

        $stats = [
            'total_revenue' => Order::whereNotIn('status', ['cancelled', 'refunded'])->sum('total'),
            'pending'       => Order::where('status', 'pending')->count(),
            'processing'    => Order::where('status', 'processing')->count(),
            'today_orders'  => Order::whereDate('created_at', today())->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats', 'request'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Order::getStatusLabels()))
        ]);

        $order->update(['status' => $validated['status']]);

        // 📤 Déclenche notifications temps réel (In-App + SMS si numéro présent)
        if ($order->user) {
            $order->user->notify(new OrderStatusUpdated($order, $validated['status']));
        }

        return back()->with('success', "Statut de la commande #{$order->id} mis à jour & notifications envoyées.");
    }

    public function exportCsv()
    {
        $orders = Order::latest()->get();
        $filename = 'commandes_' . now()->format('Y-m-d_H-i') . '.csv';

        return new StreamedResponse(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Email', 'Total', 'Statut', 'Méthode', 'Date']);
            foreach ($orders as $order) {
                fputcsv($handle, [$order->id, $order->email, $order->total, $order->status, $order->delivery_method, $order->created_at]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportPdf()
    {
        $orders = Order::latest()->limit(50)->get();
        $pdf = Pdf::loadView('admin.orders.pdf-report', compact('orders'));
        return $pdf->download('rapport_commandes_' . now()->format('Y-m-d') . '.pdf');
    }
}