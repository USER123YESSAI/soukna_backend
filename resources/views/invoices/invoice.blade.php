<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $order->order_number }} — Soukna</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #f8fafc; color: #0f172a; margin: 0; padding: 40px 20px; font-size: 14px; }
        .invoice-card { max-width: 800px; margin: 0 auto; background: #ffffff; padding: 48px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f1f5f9; padding-bottom: 28px; margin-bottom: 32px; }
        .logo { font-size: 26px; font-weight: 800; color: #4f46e5; letter-spacing: -0.5px; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { margin: 0; font-size: 24px; color: #0f172a; font-weight: 800; }
        .invoice-details { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 36px; }
        .details-col h3 { margin: 0 0 8px; font-size: 13px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
        .table th { background: #f8fafc; padding: 12px 14px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 700; text-align: left; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .table td { padding: 14px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .totals { margin-left: auto; width: 320px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; color: #475569; font-size: 14px; }
        .totals-row.grand-total { border-top: 2px solid #0f172a; padding-top: 12px; margin-top: 8px; font-size: 18px; font-weight: 800; color: #0f172a; }
        .actions { text-align: center; margin-top: 32px; }
        .print-btn { background: #4f46e5; color: white; border: none; padding: 12px 28px; border-radius: 99px; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3); transition: all 0.2s; }
        .print-btn:hover { background: #4338ca; }
        @media print {
            body { background: white; padding: 0; }
            .invoice-card { box-shadow: none; border: none; padding: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-card">
        <div class="invoice-header">
            <div>
                <div class="logo">SOUKNA</div>
                <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Plateforme Marketplace E-Commerce</p>
                <p style="margin: 2px 0 0; color: #94a3b8; font-size: 12px;">contact@soukna.com</p>
            </div>
            <div class="invoice-title">
                <h1>FACTURE</h1>
                <p style="margin: 4px 0 0; font-weight: 700; color: #4f46e5;">#{{ $order->order_number }}</p>
                <p style="margin: 4px 0 0; color: #64748b; font-size: 12px;">Date : {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="invoice-details">
            <div class="details-col">
                <h3>Facturé à :</h3>
                <p style="margin: 0; font-weight: 700; font-size: 15px;">{{ $order->buyer?->name ?? 'Client Soukna' }}</p>
                <p style="margin: 4px 0 0; color: #475569;">Email : {{ $order->buyer?->email }}</p>
                <p style="margin: 2px 0 0; color: #475569;">Tél : {{ $order->shipping_phone }}</p>
            </div>
            <div class="details-col">
                <h3>Adresse de livraison :</h3>
                <p style="margin: 0; color: #475569;">{{ $order->shipping_address }}</p>
                <p style="margin: 2px 0 0; color: #475569;">{{ $order->shipping_city }} ({{ $order->shipping_postal_code }})</p>
                <p style="margin: 4px 0 0; color: #0f172a;">Mode de paiement : <strong>{{ strtoupper($order->payment_method ?? 'COD') }}</strong> ({{ strtoupper($order->payment_status ?? 'PENDING') }})</p>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Description de l'article</th>
                    <th style="text-align: center;">Qté</th>
                    <th style="text-align: right;">Prix unitaire</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong style="color: #0f172a;">{{ $item->product?->title ?? 'Article' }}</strong>
                            @if($item->seller)
                                <div style="font-size: 12px; color: #64748b;">Vendeur : {{ $item->seller->name }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->unit_price, 2, ',', ' ') }} FCFA</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($item->subtotal, 2, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>Sous-total articles :</span>
                <span>{{ number_format($order->items->sum('subtotal'), 2, ',', ' ') }} FCFA</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="totals-row" style="color: #16a34a; font-weight: 600;">
                    <span>Remise Coupon :</span>
                    <span>-{{ number_format($order->discount_amount, 2, ',', ' ') }} FCFA</span>
                </div>
            @endif
            <div class="totals-row">
                <span>Frais de livraison :</span>
                <span>{{ $order->shipping_cost > 0 ? number_format($order->shipping_cost, 2, ',', ' ') . ' FCFA' : 'Gratuit' }}</span>
            </div>
            <div class="totals-row grand-total">
                <span>Montant Net :</span>
                <span>{{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <div class="actions">
            <button class="print-btn" onclick="window.print()">🖨️ Imprimer ou Enregistrer en PDF</button>
        </div>
    </div>
</body>
</html>
