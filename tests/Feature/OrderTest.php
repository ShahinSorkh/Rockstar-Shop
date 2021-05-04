<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderCustomization;
use App\Models\Product;
use App\Models\ProductCustomization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_unautheticated_user_fails_to_place_order()
    {
        $product = Product::factory()->create();
        $this->postJson('/api/orders', [
            'product_id' => $product->id,
        ])
            ->assertUnauthorized();
    }

    public function test_user_can_place_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $response = $this->actingAs($user, 'api')
            ->postJson('/api/orders', [
                'product_id' => $product->id,
            ]);

        $response->assertSuccessful()
            ->assertJson(['data' => [
                'product' => $product->name,
                'price' => $product->price,
                'status' => Order::STATUS_WAITING,
            ]]);
        $this->assertDatabaseHas('orders', [
            'status' => Order::STATUS_WAITING,
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => $product->price,
        ]);
    }

    public function test_user_can_place_order_with_customizations()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $customization = $product->customizations->first();

        $this->actingAs($user, 'api')
            ->postJson('/api/orders', [
                'product_id' => $product->id,
                'customizations' => [[
                    'product_customization_id' => $customization->id,
                    'option' => $customization->options[0],
                ]],
            ])
            ->assertSuccessful()
            ->assertJson(['data' => [
                'product' => $product->name,
                'price' => $product->price,
                'status' => Order::STATUS_WAITING,
                'customizations' => [[
                    'name' => $customization->name,
                    'option' => $customization->options[0],
                ]],
            ]]);
        $this->assertDatabaseHas('orders', [
            'product_id' => $product->id,
        ])->assertDatabaseHas('order_customizations', [
            'product_customization_id' => $customization->id,
        ]);
    }

    public function test_unautheticated_user_fails_to_cancel_order()
    {
        $order = Order::factory()->create();
        $this->deleteJson("/api/orders/{$order->id}")
            ->assertUnauthorized();
    }

    public function test_user_can_cancel_waiting_order()
    {
        $order = Order::factory()->waiting()->create();
        $this->actingAs($order->user, 'api')
            ->deleteJson("/api/orders/{$order->id}")
            ->assertSuccessful();
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_user_cannot_cancel_order_if_not_on_waiting()
    {
        $order = Order::factory()->notWaiting()->create();
        $this->actingAs($order->user, 'api')
            ->deleteJson("/api/orders/{$order->id}")
            ->assertForbidden();
    }

    public function test_user_cannot_cancel_others_orders()
    {
        $user = User::factory()->create();
        $order = Order::factory()->waiting()->create();
        $this->actingAs($user, 'api')
            ->deleteJson("/api/orders/{$order->id}")
            ->assertForbidden();
    }

    public function test_user_can_change_waiting_order()
    {
        $order = Order::factory()->waiting()->create();
        $product = Product::factory()->create();
        $this->actingAs($order->user, 'api')
            ->patchJson("/api/orders/{$order->id}", [
                'product_id' => $product->id,
            ])
            ->assertSuccessful()
            ->assertJson(['data' => [
                'id' => $order->id,
                'product' => $product->name,
            ]]);
        $this->assertDatabaseMissing('orders', ['product_id' => $order->product_id]);
        $this->assertDatabaseHas('orders', ['product_id' => $product->id]);
    }

    public function test_user_can_change_waiting_order_customization()
    {
        $product = Product::factory()->has(
            ProductCustomization::factory(),
            'customizations'
        )->create();
        $customizations = $product->customizations;
        $order = Order::factory()
            ->waiting()
            ->has(
                OrderCustomization::factory()->state([
                    'product_customization_id' => $customizations[0]->id,
                    'option' => $customizations[0]->options[0],
                ]),
                'customizations'
            )
            ->create();
        $this->actingAs($order->user, 'api')
            ->patchJson("/api/orders/{$order->id}", [
                'product_id' => $product->id,
                'customizations' => [[
                    'product_customization_id' => $customizations[0]->id,
                    'option' => $customizations[0]->options[1],
                ], [
                    'product_customization_id' => $customizations[1]->id,
                    'option' => $customizations[1]->options[0],
                ]]
            ])
            ->assertSuccessful()
            ->assertJsonFragment([
                'customizations' => [[
                    'name' => $customizations[0]->name,
                    'option' => $customizations[0]->options[1],
                ], [
                    'name' => $customizations[1]->name,
                    'option' => $customizations[1]->options[0],
                ]],
            ]);
    }

    public function test_user_cannot_change_order_if_not_on_waiting()
    {
        $order = Order::factory()->notWaiting()->create();
        $product = Product::factory()->create();
        $this->actingAs($order->user, 'api')
            ->patchJson("/api/orders/{$order->id}", [
                'product_id' => $product->id
            ])
            ->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_change_orders()
    {
        $order = Order::factory()->waiting()->create();
        $product = Product::factory()->create();
        $this->patchJson("/api/orders/{$order->id}", [
            'product_id' => $product->id,
        ])
            ->assertUnauthorized();
    }

    public function test_user_cannot_change_others_orders()
    {
        $order = Order::factory()->waiting()->create();
        $product = Product::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user, 'api')
            ->patchJson("/api/orders/{$order->id}", [
                'product_id' => $product->id,
            ])
            ->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_get_orders()
    {
        Order::factory()->count(5)->create();
        $this->getJson('/api/orders')
            ->assertUnauthorized();
    }

    public function test_user_can_get_her_orders()
    {
        $user = User::factory()->create();
        Order::factory()->state(['user_id' => $user->id])->count(5)->create();
        $this->actingAs($user, 'api')
            ->getJson('/api/orders')
            ->assertSuccessful()
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure(['data' => [[
                'id', 'status', 'product', 'price',
                'customizations' => []
            ]]]);
    }

    public function test_user_cannot_place_order_on_non_existing_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api')
            ->postJson('/api/orders', ['product_id' => 1000])
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_user_cannot_order_with_non_existing_customization()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $this->actingAs($user, 'api')
            ->postJson('/api/orders', [
                'product_id' => $product->id,
                'customizations' => [[
                    'product_customization_id' => 1000,
                    'option' => 'whatever',
                ]]
            ])
            ->assertJsonValidationErrors([
                'customizations.0.product_customization_id',
                // 'customizations.0.option', FIXME
            ]);
    }
}
