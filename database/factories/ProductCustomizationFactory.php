<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCustomization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class ProductCustomizationFactory extends Factory
{
    const CUSTOMIZATIONS = [
        ['name' => 'Milk', 'options' => ['skim', 'semi', 'whole']],
        ['name' => 'Size', 'options' => ['small', 'medium', 'large']],
        ['name' => 'Shots', 'options' => ['single', 'double', 'triple']],
        ['name' => 'Kind', 'options' => ['chocolate chip', 'ginger']],
    ];

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCustomization::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return array_replace(
            ['product_id' => Product::factory()],
            Arr::random(self::CUSTOMIZATIONS),
        );
    }

    public function consumeLocation()
    {
        return $this->state(fn () => [
            'name' => 'Consume location',
            'options' => ['in shop', 'take away'],
        ]);
    }
}
