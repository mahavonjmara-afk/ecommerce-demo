<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 20px; }
        h1 { color: #1E40AF; font-size: 18px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f1f5f9; text-align: left; padding: 8px; border-bottom: 2px solid #cbd5e1; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .status-pending { color: #d97706; }
        .status-processing { color: #2563eb; }
        .status-shipped { color: #7c3aed; }
        .status-delivered { color: #059669; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <h1>Rapport des Commandes - E-Shop</h1>
    <p>Généré le {{ now()->format('d/m/Y à H:i:s') }} | {{ $orders->count() }} commandes</p>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>Email</th><th>Ville</th><th>Statut</th><th>Total</th><th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->email }}</td>
                <td>{{ $order->shipping_city }}</td>
                <td class="status-{{ $order->status }}">{{ $order->status_label }}</td>
                <td>{{ number_format($order->total, 2, ',', ' ') }} €</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document interne - E-Shop Admin Panel - Confidentiel
    </div>
</body>
</html>