<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'users_count' => User::query()->count(),
            'products_count' => Product::query()->withTrashed()->count(),
            'orders_count' => Order::query()->count(),
            'total_revenue' => Order::query()
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_amount'),
        ]);
    }

    public function exportOrders()
    {
        $orders = Order::query()
            ->with(['buyer:id,name,email', 'items'])
            ->orderByDesc('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="commandes_admin_' . date('Y-m-d_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['N° Commande', 'Date', 'Acheteur', 'Email Acheteur', 'Total (FCFA)', 'Statut', 'Paiement', 'Statut Paiement', 'Nombre Articles'], ';');

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at?->format('d/m/Y H:i') ?? '',
                    $order->buyer?->name ?? 'Inconnu',
                    $order->buyer?->email ?? '',
                    number_format((float)$order->total_amount, 2, ',', ' '),
                    $order->status,
                    $order->payment_method ?? 'N/A',
                    $order->payment_status ?? 'pending',
                    $order->items->count(),
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
