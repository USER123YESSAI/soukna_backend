<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class EnsureCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:ensure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure default categories exist in the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultCategories = ['Électronique', 'Maison', 'Mode', 'Loisirs'];
        $created = 0;
        $existing = 0;

        foreach ($defaultCategories as $name) {
            $category = Category::query()->where('slug', Str::slug($name))->first();
            
            if ($category) {
                $existing++;
                $this->info("Category '{$name}' already exists (ID: {$category->id})");
            } else {
                Category::query()->create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => "Catégorie {$name}",
                ]);
                $created++;
                $this->info("Category '{$name}' created");
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  - Existing categories: {$existing}");
        $this->info("  - New categories created: {$created}");
        $this->info("  - Total categories in database: " . Category::count());

        return self::SUCCESS;
    }
}
