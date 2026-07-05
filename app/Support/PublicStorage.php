<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

final class PublicStorage
{
    private static array $fallbackMapping = [
        'demo-lampe-design.jpg' => '0CKYhJtQlgVD60BawdAnyqggYIyO2RxxlMQwTRpc.jpg',
        'demo-casque-audio.jpg' => '3Uxmnk3ihRiJ9tKF37v6v39oqiz8uGeKQAbYuytr.jpg',
        'demo-t-shirt-bio.jpg' => 'AMCzEE9clfz2vo8HArJOMohCeigDXLtwmVWWhPv5.png',
    ];

    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            Log::warning('PublicStorage: path is null or empty');
            return null;
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
