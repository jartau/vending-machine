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

    public function testGetProductsStatus(): void
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();

        $dispenser = new ProductDispenser(new ProductRepository());
        $result = $dispenser->getProductsStatus();

        $this->assertTrue($result->contains($product1));
        $this->assertTrue($result->contains($product2));
        $this->assertTrue($result->contains($product3));
        $this->assertCount(3, $result);

    }

    public function testAddStock(): void
    {
        Product::factory()->create(['code' => 'WATER', 'stock' => 2]);
        Product::factory()->create(['code' => 'JUICE', 'stock' => 2]);

        $dispenser = new ProductDispenser(new ProductRepository());

        $dispenser->addStock('WATER', 8);
        $this->assertDatabaseHas('products', ['code' => 'WATER', 'stock' => 10]);

        $this->expectException(ProductException::class);
        $dispenser->addStock('JUICE', -5);

        $this->expectException(ProductException::class);
        $dispenser->addStock('MILK', 5);
    }
}
