<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCustomization;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()
            ->has(ProductCustomization::factory(), 'customizations')
            ->count(3)
            ->create();
        Product::factory()
            ->count(2)
            ->create();
    }
}
