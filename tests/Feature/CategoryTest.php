<?php

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

test('categories endpoint returns categories', function () {
    // Créer des catégories de test
    Category::factory()->create(['name' => 'Test Category 1']);
    Category::factory()->create(['name' => 'Test Category 2']);

    $response = $this->getJson('/api/categories');

    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonStructure([
            '*' => ['id', 'name', 'slug', 'description', 'icon', 'products_count'],
        ]);
});

test('categories endpoint returns empty array when no categories', function () {
    $response = $this->getJson('/api/categories');

    $response->assertStatus(200)
        ->assertJson([]);
});

test('category seeder creates categories', function () {
    $this->seed(\Database\Seeders\CategorySeeder::class);

    $this->assertDatabaseCount('categories', 4);
    
    $categories = Category::all();
    expect($categories)->toHaveCount(4);
    expect($categories->pluck('name')->toArray())->toContain('Électronique', 'Maison', 'Mode', 'Loisirs');
});
