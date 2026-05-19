<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.4; color: #222; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #1E40AF; }
        .logo { font-size: 20px; font-weight: bold; color: #1E40AF; }
        .meta { text-align: right; }
        .address-box { background: #f8fafc; padding: 12px; border-radius: 4px; margin-bottom: 20px; border-left: 3px solid #1E40AF; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #f1f5f9; text-align: left; padding: 8px; font-weight: 600; border-bottom: 2px solid #cbd5e1; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .totals { margin-top: 20px; text-align: right; }
        .totals div { margin: 4px 0; }
        .final-total { font-size: 16px; font-weight: bold; color: #1E40AF; margin-top: 8px; }
        .footer { margin-top: 40px; padding-top: 10px; border-top: 1px solid #cbd5e1; text-align: center; font-size: 9px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">E-SHOP</div>
        <div class="meta">
            <strong>Facture #{{ $order->id }}</strong><br>
            Émise le {{ $order->created_at->format('d/m/Y à H:i') }}<br>
            Statut : <span style="color: #10B981;">Payée</span>
        </div>
    </div>

    <div class="address-box">
        <strong>Facturé à :</strong><br>
        {{ $order->email }}<br>
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
        {{ $order->shipping_country }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th style="text-align: center; width: 60px;">Qté</th>
                <th style="text-align: right; width: 80px;">P.U. HT</th>
                <th style="text-align: right; width: 80px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">{{ number_format($item->price / 1.2, 2, ',', ' ') }} €</td>
                <td style="text-align: right;">{{ number_format($item->price * $item->quantity, 2, ',', ' ') }} €</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>Sous-total HT : {{ number_format($order->subtotal / 1.2, 2, ',', ' ') }} €</div>
        <div>TVA (20%) : {{ number_format($order->tax, 2, ',', ' ') }} €</div>
        <div>Frais de livraison : {{ number_format($order->delivery_cost, 2, ',', ' ') }} €</div>
        <div class="final-total">Total TTC : {{ number_format($order->total, 2, ',', ' ') }} €</div>
    </div>

    <div class="footer">
        Merci pour votre confiance. Document généré automatiquement. Conservez-le pour vos justificatifs fiscaux.<br>
        E-Shop SAS - 10 Rue du Commerce, 75015 Paris - contact@eshop.fr
    </div>
</body>
</html>