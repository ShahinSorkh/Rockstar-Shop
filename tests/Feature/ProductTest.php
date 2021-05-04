<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_denied_when_not_authenticated()
    {
        $this->getJson('/api/menu')
            ->assertUnauthorized();
    }

    public function test_get_menu_items()
    {
        Product::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->create(), 'api')
            ->getJson('/api/menu');

        $response->assertSuccessful()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [[
                'id', 'name', 'price',
                'customizations' => [['name', 'options']]
            ]]]);
    }
}
