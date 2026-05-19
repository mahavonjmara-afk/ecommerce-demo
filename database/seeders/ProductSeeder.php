<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Électronique', 'Mode', 'Maison', 'Sports'];
        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
                'slug' => Str::slug($cat),
                'description' => "Découvrez nos articles de $cat",
                'is_active' => true
            ]);
        }

        $products = [
            ['name' => 'Casque Bluetooth Premium', 'price_ht' => 45.00, 'price_ttc' => 54.00, 'stock' => 15, 'is_featured' => true],
            ['name' => 'Montre Connectée Sport',   'price_ht' => 89.99, 'price_ttc' => 107.99, 'stock' => 8,  'is_featured' => false],
            ['name' => 'Sneakers Urban Classic',   'price_ht' => 65.00, 'price_ttc' => 78.00,  'stock' => 22, 'is_featured' => true],
            ['name' => 'Lampe Design LED',         'price_ht' => 25.50, 'price_ttc' => 30.60,  'stock' => 30, 'is_featured' => false],
            ['name' => 'Sac à dos Randonnée',      'price_ht' => 55.00, 'price_ttc' => 66.00,  'stock' => 12, 'is_featured' => true],
        ];

        $cats = Category::all();
        foreach ($products as $i => $p) {
            Product::create([
                'category_id' => $cats->random()->id,
                'name' => $p['name'],
                'slug' => Str::slug($p['name']),
                'description' => "Description détaillée de {$p['name']}. Matériaux premium, garantie 2 ans.",
                'short_description' => "Un produit incontournable.",
                'price_ht' => $p['price_ht'],
                'price_ttc' => $p['price_ttc'],
                'stock' => $p['stock'],
                'image' => "https://placehold.co/400x400/e2e8f0/1e293b?text=Produit+" . ($i + 1),
                'is_featured' => $p['is_featured'],
                'is_active' => true
            ]);
        }
    }
}
