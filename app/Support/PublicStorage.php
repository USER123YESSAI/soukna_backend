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

        // Fallback: si le fichier n'existe pas, utiliser un fichier réel existant
        if (!Storage::disk('public')->exists($path)) {
            Log::warning('PublicStorage: file does not exist, trying fallback', ['path' => $path]);
            
            if (isset(self::$fallbackMapping[$path])) {
                $fallbackPath = self::$fallbackMapping[$path];
                if (Storage::disk('public')->exists($fallbackPath)) {
                    Log::info('PublicStorage: using fallback file', ['original' => $path, 'fallback' => $fallbackPath]);
                    $path = $fallbackPath;
                }
            }
        }

        $url = Storage::disk('public')->url($path);
        
        Log::info('PublicStorage URL generated', [
            'path' => $path,
            'url' => $url,
            'disk' => 'public',
            'app_url' => config('app.url'),
            'asset_url' => config('app.asset_url'),
        ]);

        return $url;
    }
}
