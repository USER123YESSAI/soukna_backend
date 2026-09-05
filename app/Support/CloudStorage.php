<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class CloudStorage
{
    /**
     * Stocke une image : sur MinIO/S3 (URL permanente) si configuré,
     * sinon en fallback sur le stockage local 'public'.
     */
    public static function storeImage(UploadedFile $file, string $folder = 'products'): string
    {
        // 1. Cloudinary upload if CLOUDINARY_URL is configured
        $cloudinaryUrl = env('CLOUDINARY_URL');
        if (!empty($cloudinaryUrl)) {
            try {
                $parsed = parse_url($cloudinaryUrl);
                if ($parsed && !empty($parsed['host']) && !empty($parsed['user']) && !empty($parsed['pass'])) {
                    $cloudName = $parsed['host'];
                    $apiKey = $parsed['user'];
                    $apiSecret = $parsed['pass'];
                    $timestamp = time();
                    $signature = sha1("folder={$folder}&timestamp={$timestamp}{$apiSecret}");

                    $response = \Illuminate\Support\Facades\Http::attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    )->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                        'api_key' => $apiKey,
                        'timestamp' => $timestamp,
                        'folder' => $folder,
                        'signature' => $signature,
                    ]);

                    if ($response->successful()) {
                        $secureUrl = $response->json('secure_url');
                        if ($secureUrl) {
                            Log::info('CloudStorage: image uploaded to Cloudinary successfully', ['url' => $secureUrl]);
                            return $secureUrl;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('CloudStorage: Cloudinary upload failed, trying next storage', ['error' => $e->getMessage()]);
            }
        }

        $minioConfigured = !empty(env('AWS_ENDPOINT')) || !empty(env('AWS_BUCKET'));

        if ($minioConfigured) {
            try {
                $path = $file->store($folder, 's3');
                
                if ($path) {
                    Log::info('CloudStorage: image uploaded to MinIO/S3 successfully', [
                        'path' => $path,
                    ]);
                    return $path;
                }
            } catch (\Throwable $e) {
                Log::error('CloudStorage: MinIO/S3 upload failed, falling back to local public storage', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Fallback local (stockage standard Laravel sur disque 'public')
        $localPath = $file->store($folder, 'public');
        Log::info('CloudStorage: image stored locally on public disk', ['path' => $localPath]);

        return $localPath;
    }
}
