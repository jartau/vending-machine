<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Example 1: Buy Soda with exact change
     *
     * Input: 1, 0.25, 0.25, GET-SODA
     * Output: SODA
     *
     * @return void
     */
    public function test_example1(): void
    {
        $this->post('order/insert-coin', ['value' => 1])->assertStatus(200);
        $this->post('order/insert-coin', ['value' => 0.25])->assertStatus(200);
        $this->post('order/insert-coin', ['value' => 0.25])->assertStatus(200);

        $this->post('order/get', ['code' => 'SODA'])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'product' => 'SODA',
                    'exchange' => []
                ]
            ]);

        $this->assertDatabaseHas('products', ['code' => 'SODA', 'stock' => 2]);
        $this->assertDatabaseHas('products', ['code' => 'JUICE', 'stock' => 5]);
        $this->assertDatabaseHas('products', ['code' => 'WATER', 'stock' => 10]);

        $this->assertDatabaseHas('coins', ['value' => 1, 'stock' => 25, 'earned' => 1]);
        $this->assertDatabaseHas('coins', ['value' => 0.25, 'stock' => 15, 'earned' => 2]);
        $this->assertDatabaseHas('coins', ['value' => 0.1, 'stock' => 50, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.05, 'stock' => 100, 'earned' => 0]);
    }

    /**
     * Example 2: Start adding money, but user ask for return coin
     *
     * Input: 0.10, 0.10, RETURN-COIN
     * Output: 0.10, 0.10
     *
     * @return void
     */
    public function test_example2(): void
    {

        $this->post('order/insert-coin', ['value' => 0.10])->assertStatus(200);
        $this->post('order/insert-coin', ['value' => 0.10])->assertStatus(200);

        $this->get('order/return-coin')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'product' => null,
                    'exchange' => [0.1, 0.1]
                ]
            ]);

        $this->assertDatabaseHas('products', ['code' => 'SODA', 'stock' => 3]);
        $this->assertDatabaseHas('products', ['code' => 'JUICE', 'stock' => 5]);
        $this->assertDatabaseHas('products', ['code' => 'WATER', 'stock' => 10]);

        $this->assertDatabaseHas('coins', ['value' => 1, 'stock' => 25, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.25, 'stock' => 15, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.1, 'stock' => 50, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.05, 'stock' => 100, 'earned' => 0]);
    }

    /**
     * Example 3: Buy Water without exact change
     *
     * Input: 1, GET-WATER
     * Output: WATER, 0.25, 0.10
     *
     * @return void
     */
    public function test_example3(): void
    {
        $this->post('order/insert-coin', ['value' => 1])->assertStatus(200);

        $this->post('order/get', ['code' => 'WATER'])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'product' => 'WATER',
                    'exchange' => [0.25, 0.1]
                ]
            ]);

        $this->assertDatabaseHas('products', ['code' => 'SODA', 'stock' => 3]);
        $this->assertDatabaseHas('products', ['code' => 'JUICE', 'stock' => 5]);
        $this->assertDatabaseHas('products', ['code' => 'WATER', 'stock' => 9]);

        $this->assertDatabaseHas('coins', ['value' => 1, 'stock' => 25, 'earned' => 1]);
        $this->assertDatabaseHas('coins', ['value' => 0.25, 'stock' => 14, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.1, 'stock' => 49, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 0.05, 'stock' => 100, 'earned' => 0]);
    }

    /**
     * Test for invalid inputs
     *
     * @return void
     */
    public function test_invalidInputs(): void
    {
        $this->post('order/insert-coin', ['value' => 2])
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid coin value'
            ]);

        $this->post('order/get', ['code' => 'LEMONADE'])
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid product code'
            ]);
    }
}
