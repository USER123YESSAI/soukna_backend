<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mise à jour de commande</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f8fafc; color: #0f172a; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
        .header { background: #0f172a; color: #ffffff; padding: 28px 24px; text-align: center; }
        .content { padding: 24px; }
        .footer { background: #f8fafc; padding: 16px 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 22px;">Statut de votre commande mis à jour</h1>
            <p style="margin: 6px 0 0; color: #94a3b8; font-size: 14px;">Commande #{{ $order->order_number }}</p>
        </div>
        <div class="content">
            <p>Bonjour <strong>{{ $order->buyer?->name ?? 'Cher client' }}</strong>,</p>
            <p>Votre commande est désormais : <strong style="color: #4f46e5; text-transform: uppercase;">{{ $status }}</strong>.</p>
            <p>Vous pouvez suivre l'état de votre colis à tout moment depuis votre espace client Soukna.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Soukna Marketplace.
        </div>
    </div>
</body>
</html>
