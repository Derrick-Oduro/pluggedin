<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Storage', 'slug' => 'storage'],
            ['name' => 'Memory', 'slug' => 'memory'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Add sample products
        $storage = Category::where('slug', 'storage')->first();
        $memory = Category::where('slug', 'memory')->first();
        $accessories = Category::where('slug', 'accessories')->first();

        // Storage products
        Product::updateOrCreate([
            'name' => 'Samsung 980 PRO SSD 1TB',
        ], [
            'name' => 'Samsung 980 PRO SSD 1TB',
            'description' => 'High-performance NVMe SSD with PCIe 4.0 interface. Read speeds up to 7,000 MB/s.',
            'price' => 129.99,
            'category_id' => $storage->id,
            'stock_quantity' => 25,
        ]);

        Product::updateOrCreate([
            'name' => 'WD Blue SSD 500GB',
        ], [
            'name' => 'WD Blue SSD 500GB',
            'description' => 'Reliable SATA SSD for everyday computing. Read speeds up to 560 MB/s.',
            'price' => 49.99,
            'category_id' => $storage->id,
            'stock_quantity' => 50,
        ]);

        Product::updateOrCreate([
            'name' => 'Crucial MX500 2TB',
        ], [
            'name' => 'Crucial MX500 2TB',
            'description' => 'Affordable high-capacity SSD with DRAM cache. Perfect for large storage needs.',
            'price' => 179.99,
            'category_id' => $storage->id,
            'stock_quantity' => 15,
        ]);

        // Memory products
        Product::updateOrCreate([
            'name' => 'Corsair Vengeance 16GB (2x8GB) DDR4',
        ], [
            'name' => 'Corsair Vengeance 16GB (2x8GB) DDR4',
            'description' => 'High-performance DDR4 RAM at 3200MHz. Perfect for gaming and productivity.',
            'price' => 64.99,
            'category_id' => $memory->id,
            'stock_quantity' => 40,
        ]);

        Product::updateOrCreate([
            'name' => 'Kingston Fury 32GB (2x16GB) DDR4',
        ], [
            'name' => 'Kingston Fury 32GB (2x16GB) DDR4',
            'description' => 'Premium DDR4 RAM kit at 3600MHz. Ideal for heavy multitasking and content creation.',
            'price' => 119.99,
            'category_id' => $memory->id,
            'stock_quantity' => 30,
        ]);

        Product::updateOrCreate([
            'name' => 'G.Skill Trident Z5 16GB DDR5',
        ], [
            'name' => 'G.Skill Trident Z5 16GB DDR5',
            'description' => 'Next-gen DDR5 memory at 6000MHz. For cutting-edge performance.',
            'price' => 149.99,
            'category_id' => $memory->id,
            'stock_quantity' => 20,
        ]);

        // Accessories
        Product::updateOrCreate([
            'name' => 'SSD Installation Kit',
        ], [
            'name' => 'SSD Installation Kit',
            'description' => 'Complete toolkit for SSD installation including screwdrivers, SATA cables, and mounting brackets.',
            'price' => 19.99,
            'category_id' => $accessories->id,
            'stock_quantity' => 100,
        ]);

        Product::updateOrCreate([
            'name' => 'Thermal Paste Premium',
        ], [
            'name' => 'Thermal Paste Premium',
            'description' => 'High-quality thermal compound for optimal heat transfer. Essential for upgrades.',
            'price' => 9.99,
            'category_id' => $accessories->id,
            'stock_quantity' => 150,
        ]);
    }
}
