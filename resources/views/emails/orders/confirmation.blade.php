<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #f4f4f7; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1E40AF; color: #fff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; }
        .content { padding: 20px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin: 16px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px; }
        th { background: #f1f5f9; text-align: left; padding: 10px; border-bottom: 2px solid #cbd5e1; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .total { text-align: right; font-size: 16px; font-weight: bold; color: #1E40AF; margin: 20px 0; }
        .btn { display: inline-block; background: #1E40AF; color: #fff !important; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 500; margin-top: 10px; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .content { padding: 16px !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Commande confirmée !</h1>
            <p style="margin: 4px 0 0; opacity: 0.9;">Merci pour votre achat</p>
        </div>
        <div class="content">
            <p>Bonjour,</p>
            <p>Votre commande <strong>#{{ $order->id }}</strong> a bien été enregistrée et est en cours de traitement.</p>
            
            <div class="info-box">
                📧 <strong>Email :</strong> {{ $order->email }}<br>
                📦 <strong>Mode de livraison :</strong> {{ ucfirst($order->delivery_method) }}<br>
                📍 <strong>Adresse :</strong> {{ $order->shipping_address }}, {{ $order->shipping_postal_code }} {{ $order->shipping_city }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th style="text-align: center;">Qté</th>
                        <th style="text-align: right;">Prix</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->price * $item->quantity, 2, ',', ' ') }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                Total TTC : {{ number_format($order->total, 2, ',', ' ') }} €
            </div>

            <p style="font-size: 13px; color: #64748b;">📎 Votre facture PDF est jointe à cet email. Conservez-la pour vos justificatifs.</p>
            
            <a href="{{ url('/commande/' . $order->id . '/merci') }}" class="btn">Voir ma commande</a>
        </div>
        <div class="footer">
            © {{ date('Y') }} E-Shop. Tous droits réservés.<br>
            Vous recevez cet email car vous avez effectué une commande sur notre boutique.
        </div>
    </div>
</body>
</html>