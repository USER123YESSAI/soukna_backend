<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

final class PublicStorage
{
    private static array $fallbackMapping = [
        'products/demo-lampe-design.jpg' => 'products/0CKYhJtQlgVD60BawdAnyqggYIyO2RxxlMQwTRpc.jpg',
        'products/demo-casque-audio.jpg' => 'products/3Uxmnk3ihRiJ9tKF37v6v39oqiz8uGeKQAbYuytr.jpg',
        'products/demo-t-shirt-bio.jpg' => 'products/AMCzEE9clfz2vo8HArJOMohCeigDXLtwmVWWhPv5.png',
        'demo-lampe-design.jpg' => 'products/0CKYhJtQlgVD60BawdAnyqggYIyO2RxxlMQwTRpc.jpg',
        'demo-casque-audio.jpg' => 'products/3Uxmnk3ihRiJ9tKF37v6v39oqiz8uGeKQAbYuytr.jpg',
        'demo-t-shirt-bio.jpg' => 'products/AMCzEE9clfz2vo8HArJOMohCeigDXLtwmVWWhPv5.png',
    ];

    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            Log::warning('PublicStorage: path is null or empty');
            return null;
        }

        // Correction automatique des liens de pages HTML ImgBB vers leurs URLs d'images directes
        $ibbMapping = [
            'https://ibb.co/Nd70kZY1' => 'https://i.ibb.co/sdtTDy35/habit-homme.png',
            'https://ibb.co/VcnxGyzR' => 'https://i.ibb.co/FLZX1vy2/chaussure-femme.png',
            'https://ibb.co/W4v51Vsw' => 'https://i.ibb.co/nNMmSPRv/chaussure-homme.png',
            'https://ibb.co/Zp4RJ9TQ' => 'https://i.ibb.co/pBV6PqXH/iphone17.png',
        ];
        if (isset($ibbMapping[$path])) {
            return $ibbMapping[$path];
        }

        // Si l'image est une URL complète (ex: Cloudinary, S3, Unsplash), on la retourne directement
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Fallback rapide pour les noms de démo
        if (isset(self::$fallbackMapping[$path])) {
            $path = self::$fallbackMapping[$path];
        }

        // Check MinIO / S3 if configured
        $minioConfigured = !empty(env('AWS_ENDPOINT')) || !empty(env('AWS_BUCKET'));
        if ($minioConfigured) {
            try {
                if (Storage::disk('s3')->exists($path)) {
                    return Storage::disk('s3')->url($path);
                }
            } catch (\Throwable) {
                // Fallback silencieux vers le stockage public
            }
        }

        return Storage::disk('public')->url($path);
    }
}
