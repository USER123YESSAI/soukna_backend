<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix product image paths to match actual files in storage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Récupérer les fichiers réels dans storage/app/public/products/
        $realFiles = Storage::disk('public')->files('products');
        
        $this->info('Found ' . count($realFiles) . ' files in storage/app/public/products/');
        
        // Mapper les anciens noms factices vers les fichiers réels
        $mapping = [
            'demo-lampe-design.jpg' => '0CKYhJtQlgVD60BawdAnyqggYIyO2RxxlMQwTRpc.jpg',
            'demo-casque-audio.jpg' => '3Uxmnk3ihRiJ9tKF37v6v39oqiz8uGeKQAbYuytr.jpg',
            'demo-t-shirt-bio.jpg' => 'AMCzEE9clfz2vo8HArJOMohCeigDXLtwmVWWhPv5.png',
        ];

        $updated = 0;

        // Mettre à jour les produits avec les chemins corrects
        foreach ($mapping as $oldPath => $newPath) {
            $count = Product::where('image', $oldPath)->update(['image' => $newPath]);
            if ($count > 0) {
                $this->info("Updated {$count} products: {$oldPath} -> {$newPath}");
                $updated += $count;
            }
        }

        // Pour les produits avec des chemins invalides, assigner un fichier réel aléatoire
        $invalidProducts = Product::whereNotIn('image', array_values($mapping))
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->get();

        $this->info('Found ' . $invalidProducts->count() . ' products with invalid image paths');

        foreach ($invalidProducts as $product) {
            if (!empty($realFiles)) {
                $randomFile = $realFiles[array_rand($realFiles)];
                $this->info("Updating product {$product->id} ({$product->title}): {$product->image} -> {$randomFile}");
                $product->image = $randomFile;
                $product->save();
                $updated++;
            }
        }

        $this->newLine();
        $this->info("Total products updated: {$updated}");

        return self::SUCCESS;
    }
}
