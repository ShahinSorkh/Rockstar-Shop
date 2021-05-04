<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCustomization;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class ProductFactory extends Factory
{
    const PRODUCTS = [
        'Cookie',
        'Cuppoccino',
        'Espresso',
        'Hot Chocolate',
        'Latte',
        'Tea',
    ];

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Arr::random(self::PRODUCTS),
            'price' => $this->faker->randomFloat(2, 1, 10),
        ];
    }
}
