<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PublicStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'role' => ['nullable', 'string', Rule::in(['buyer', 'seller', 'admin'])],
        ]);

        $query = User::query()->orderByDesc('id');

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        $perPage = min(max($request->integer('per_page', 15), 1), 100);
        $paginator = $query->paginate($perPage);

        $paginator->through(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->role,
            'suspended_at' => $u->suspended_at?->toIso8601String(),
            'profile_image' => PublicStorage::url($u->profile_image),
            'created_at' => $u->created_at?->toIso8601String(),
        ]);

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function suspend(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            abort(422, __('Vous ne pouvez pas suspendre votre propre compte.'));
        }

        $user->forceFill(['suspended_at' => now()])->save();

        $user->tokens()->delete();

        return response()->json([
            'user' => ['id' => $user->id, 'suspended_at' => $user->suspended_at?->toIso8601String()],
            'message' => __('Utilisateur suspendu.'),
        ]);
    }

    public function activate(User $user): JsonResponse
    {
        $user->forceFill(['suspended_at' => null])->save();

        return response()->json([
            'user' => ['id' => $user->id, 'suspended_at' => null],
            'message' => __('Compte réactivé.'),
        ]);
    }

    public function exportUsers(Request $request)
    {
        $users = User::query()->orderByDesc('id')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="utilisateurs_' . date('Y-m-d_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['ID', 'Nom', 'Email', 'Rôle', 'Statut', 'Date Inscription'], ';');

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->suspended_at ? 'Suspendu' : 'Actif',
                    $user->created_at?->format('d/m/Y H:i') ?? '',
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
