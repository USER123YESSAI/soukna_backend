<?php

namespace App\Support;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

final class CloudStorage
{
    /**
     * Stocke une image : sur Cloudinary (URL permanente) si configuré,
     * sinon en fallback sur le stockage local 'public'.
     */
    public static function storeImage(UploadedFile $file, string $folder = 'products'): string
    {
        $cloudinaryUrl = env('CLOUDINARY_URL') ?: config('services.cloudinary.url');
        $cloudName = env('CLOUDINARY_CLOUD_NAME') ?: config('services.cloudinary.cloud_name');

        if (!empty($cloudinaryUrl) || !empty($cloudName)) {
            try {
                if (!empty($cloudinaryUrl)) {
                    $cloudinary = new Cloudinary($cloudinaryUrl);
                } else {
                    $cloudinary = new Cloudinary([
                        'cloud' => [
                            'cloud_name' => $cloudName,
                            'api_key'    => env('CLOUDINARY_API_KEY') ?: config('services.cloudinary.api_key'),
                            'api_secret' => env('CLOUDINARY_API_SECRET') ?: config('services.cloudinary.api_secret'),
                        ],
                        'url' => ['secure' => true],
                    ]);
                }

                $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'soukna/' . $folder,
                ]);

                if (!empty($result['secure_url'])) {
                    Log::info('CloudStorage: image uploaded to Cloudinary successfully', [
                        'secure_url' => $result['secure_url'],
                    ]);
                    return $result['secure_url'];
                }
            } catch (\Throwable $e) {
                Log::error('CloudStorage: Cloudinary upload failed, falling back to local public storage', [
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
