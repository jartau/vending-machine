<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * Test for info service user
     *
     * @return void
     */
    public function test_info(): void
    {
        $this->get('service/info')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'product_stock' => [
                        ['code' => 'WATER', 'stock' => 3],
                        ['code' => 'JUICE', 'stock' => 5],
                        ['code' => 'WATER', 'stock' => 10]
                    ],
                    'coin_status' => [
                        ['value' => 0.05, 'stock' => 100, 'earned' => 0],
                        ['value' => 0.1, 'stock' => 50, 'earned' => 0],
                        ['value' => 0.25, 'stock' => 15, 'earned' => 0],
                        ['value' => 1, 'stock' => 25, 'earned' => 0]
                    ]
                ]

            ]);
    }

    /**
     * Test for service add products action
     *
     * @return void
     */
    public function test_addProduct(): void
    {
        $this->post('service/add-product', ['code' => 'JUICE', 'quantity' => 15])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);

        $this->post('service/add-product', ['code' => 'WATER', 'quantity' => -4])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);

        $this->post('service/add-product', ['code' => 'SODA', 'quantity' => -5])
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Not enough products'
            ]);

        $this->post('service/add-product', ['code' => 'MILK', 'quantity' => 5])
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid product code'
            ]);

        $this->assertDatabaseHas('products', ['code' => 'SODA', 'stock' => 3]);
        $this->assertDatabaseHas('products', ['code' => 'JUICE', 'stock' => 20]);
        $this->assertDatabaseHas('products', ['code' => 'WATER', 'stock' => 6]);

    }

    /**
     * Test for service add products action
     *
     * @return void
     */
    public function test_addCoin(): void
    {
        $this->post('service/add-coin', ['value' => 0.05, 'quantity' => 250])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);

        $this->post('service/add-coin', ['value' => 0.1, 'quantity' => -50])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);

        $this->post('service/add-coin', ['value' => 0.25, 'quantity' => -25])
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Not enough coins'
            ]);

        $this->post('service/add-coin', ['value' => 0.5, 'quantity' => 5])
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid coin value'
            ]);

        $this->assertDatabaseHas('coins', ['value' => 1, 'stock' => 25]);
        $this->assertDatabaseHas('coins', ['value' => 0.25, 'stock' => 15]);
        $this->assertDatabaseHas('coins', ['value' => 0.1, 'stock' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.05, 'stock' => 350]);

    }

    /**
     * Test for service collect coins action
     *
     * @return void
     */
    public function test_collectCoins(): void
    {
        $this->get('service/collect-coins')->assertStatus(200);

        $this->assertDatabaseHas('coins', ['value' => 1, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.25, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.1, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.05, 'earned' => 0]);

    }

}
