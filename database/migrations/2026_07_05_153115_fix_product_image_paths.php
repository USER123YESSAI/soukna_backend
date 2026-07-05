<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Récupérer les fichiers réels dans storage/app/public/products/
        $realFiles = Storage::disk('public')->files('products');
        
        // Mapper les anciens noms factices vers les fichiers réels
        $mapping = [
            'demo-lampe-design.jpg' => '0CKYhJtQlgVD60BawdAnyqggYIyO2RxxlMQwTRpc.jpg',
            'demo-casque-audio.jpg' => '3Uxmnk3ihRiJ9tKF37v6v39oqiz8uGeKQAbYuytr.jpg',
            'demo-t-shirt-bio.jpg' => 'AMCzEE9clfz2vo8HArJOMohCeigDXLtwmVWWhPv5.png',
        ];

        // Mettre à jour les produits avec les chemins corrects
        foreach ($mapping as $oldPath => $newPath) {
            Product::where('image', $oldPath)->update(['image' => $newPath]);
        }

        // Pour les produits avec des chemins invalides, assigner un fichier réel aléatoire
        $invalidProducts = Product::whereNotIn('image', array_values($mapping))
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->get();

        foreach ($invalidProducts as $product) {
            if (!empty($realFiles)) {
                $randomFile = $realFiles[array_rand($realFiles)];
                $product->image = $randomFile;
                $product->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer les anciens chemins factices
        $mapping = [
            '0CKYhJtQlgVD60BawdAnyqggYIyO2RxxlMQwTRpc.jpg' => 'demo-lampe-design.jpg',
            '3Uxmnk3ihRiJ9tKF37v6v39oqiz8uGeKQAbYuytr.jpg' => 'demo-casque-audio.jpg',
            'AMCzEE9clfz2vo8HArJOMohCeigDXLtwmVWWhPv5.png' => 'demo-t-shirt-bio.jpg',
        ];

        foreach ($mapping as $newPath => $oldPath) {
            Product::where('image', $newPath)->update(['image' => $oldPath]);
        }
    }
};
