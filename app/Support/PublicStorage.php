<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

final class PublicStorage
{
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            Log::warning('PublicStorage: path is null or empty');
            return null;
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
