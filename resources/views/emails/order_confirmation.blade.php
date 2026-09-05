<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Commande</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #0f172a; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color: #ffffff; padding: 32px 24px; text-align: center; }
        .content { padding: 28px 24px; }
        .badge { display: inline-block; padding: 4px 12px; background: #e0e7ff; color: #4338ca; border-radius: 99px; font-weight: 700; font-size: 12px; margin-bottom: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 16px; margin-bottom: 20px; }
        .table th { text-align: left; padding: 10px 8px; border-bottom: 2px solid #e2e8f0; font-size: 13px; color: #64748b; }
        .table td { padding: 12px 8px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .total-row { font-weight: 700; font-size: 16px; color: #0f172a; }
        .footer { background: #f8fafc; padding: 16px 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">SOUKNA MARKETPLACE</span>
            <h1 style="margin: 0; font-size: 24px; font-weight: 800;">Merci pour votre commande !</h1>
            <p style="margin: 8px 0 0; color: #cbd5e1; font-size: 14px;">Commande #{{ $order->order_number }}</p>
        </div>
        <div class="content">
            <p>Bonjour <strong>{{ $order->buyer?->name ?? 'Cher client' }}</strong>,</p>
            <p>Nous avons bien reçu votre commande et nous la préparons avec soin.</p>

            <table class="table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th style="text-align: center;">Qté</th>
                        <th style="text-align: right;">Prix</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product?->title ?? 'Produit' }}</td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">{{ number_format($item->subtotal, 2, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                    @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="2" style="color: #16a34a; font-weight: 600;">Remise Coupon</td>
                            <td style="text-align: right; color: #16a34a; font-weight: 600;">-{{ number_format($order->discount_amount, 2, ',', ' ') }} FCFA</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="2">Total Payé / Dû</td>
                        <td style="text-align: right;">{{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</td>
                    </tr>
                </tbody>
            </table>

            <div style="background: #f8fafc; padding: 16px; border-radius: 12px; font-size: 13px; line-height: 1.5;">
                <strong>Adresse de livraison :</strong><br>
                {{ $order->shipping_address }}, {{ $order->shipping_city }} ({{ $order->shipping_postal_code }})<br>
                Téléphone : {{ $order->shipping_phone }}<br>
                Mode de paiement : <strong>{{ strtoupper($order->payment_method ?? 'Paiement à la livraison') }}</strong>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Soukna Marketplace — Tous droits réservés.
        </div>
    </div>
</body>
</html>
