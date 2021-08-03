<?php

namespace Tests\VendingMachine;

use App\Exceptions\ProductException;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\VendingMachine\ProductDispenser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDispenserTest extends TestCase
{

    use RefreshDatabase;

    public function testChooseProduct(): void
    {
        Product::factory()->create(['code' => 'WATER', 'stock' => 0, 'price' => 125]);
        Product::factory()->create(['code' => 'JUICE', 'stock' => 1, 'price' => 125]);
        $dispenser = new ProductDispenser(new ProductRepository());

        $this->expectException(ProductException::class);
        $dispenser->chooseProduct('MILK');

        $this->expectException(ProductException::class);
        $dispenser->chooseProduct('WATER');

        $dispenser->chooseProduct('JUICE');

        $this->assertEquals('JUICE', $dispenser->getProductCode());
    }

    public function testUpdateProductStock(): void
    {
        Product::factory()->create(['code' => 'JUICE', 'stock' => 1, 'price' => 125]);
        $dispenser = new ProductDispenser(new ProductRepository());

        $this->expectException(ProductException::class);
        $dispenser->updateProductStock();

        $dispenser->chooseProduct('JUICE');
        $dispenser->updateProductStock();

        $this->assertDatabaseHas('products', ['code' => 'JUICE', 'stock' => 0, 'price' => 125]);
    }

    public function testGetProductPrice(): void
    {
        Product::factory()->create(['code' => 'WATER', 'stock' => 1, 'price' => 125]);
        $dispenser = new ProductDispenser(new ProductRepository());
        $dispenser->chooseProduct('WATER');
        $this->assertEquals(125, $dispenser->getProductPrice());
    }

    /**
     * @throws ProductException
     */
    public function testGetProductCode(): void
    {
        Product::factory()->create(['code' => 'WATER', 'stock' => 1]);
        $dispenser = new ProductDispenser(new ProductRepository());
        $dispenser->chooseProduct('WATER');
        $this->assertEquals('WATER', $dispenser->getProductCode());
    }
}
