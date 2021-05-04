<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'price' => fn ($attrs) => Product::find($attrs['product_id'])->price,
            'status' => Arr::random(Order::STATUSES),
        ];
    }

    public function waiting()
    {
        return $this->state(fn () => ['status' => Order::STATUS_WAITING]);
    }

    public function notWaiting()
    {
        $statues = array_filter(Order::STATUSES, fn ($status) => $status !== Order::STATUS_WAITING);
        return $this->state(fn () => ['status' => Arr::random($statues)]);
    }
}
