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
