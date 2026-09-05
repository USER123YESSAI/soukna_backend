<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle commande reçue</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f8fafc; color: #0f172a; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
        .header { background: #4f46e5; color: #ffffff; padding: 28px 24px; text-align: center; }
        .content { padding: 24px; }
        .footer { background: #f8fafc; padding: 16px 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 22px;">Nouvelle commande reçue ! 🎉</h1>
            <p style="margin: 6px 0 0; color: #e0e7ff; font-size: 14px;">Commande #{{ $order->order_number }}</p>
        </div>
        <div class="content">
            <p>Bonjour <strong>{{ $seller->name }}</strong>,</p>
            <p>Un client vient de commander un ou plusieurs articles de votre boutique sur <strong>Soukna</strong>.</p>
            <p>Veuillez vous connecter à votre <strong>Espace Vendeur</strong> pour traiter et expédier la commande au plus vite.</p>
            <p>Statut actuel : <strong>{{ strtoupper($order->status) }}</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Soukna Marketplace — Espace Vendeur.
        </div>
    </div>
</body>
</html>
