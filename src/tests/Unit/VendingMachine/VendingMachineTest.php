<?php

namespace Tests\VendingMachine;

use App\Exceptions\ProductException;
use App\Models\Coin;
use App\Models\Product;
use App\Repositories\CoinRepository;
use App\Repositories\ProductRepository;
use App\VendingMachine\CoinCounter;
use App\VendingMachine\VendingMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendingMachineTest extends TestCase
{

    use RefreshDatabase;

    public function testReturnCoins()
    {
        Coin::factory()->create(['value' => 100, 'stock' => 2, 'earned' => 0]);
        Coin::factory()->create(['value' => 25, 'stock' => 1, 'earned' => 0]);

        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $machine->insertCoin(25);
        $machine->insertCoin(100);
        $this->assertEquals([25, 100], $machine->returnCoins());
        $this->assertEquals([], $machine->returnCoins());
    }

    public function testGetChange()
    {
        Coin::factory()->create(['value' => 100, 'stock' => 2, 'earned' => 0]);
        Coin::factory()->create(['value' => 25, 'stock' => 1, 'earned' => 0]);
        Coin::factory()->create(['value' => 10, 'stock' => 2, 'earned' => 0]);
        Product::factory()->create(['code' => 'WATER', 'stock' => 1, 'price' => 65]);

        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $machine->insertCoin(100);
        $machine->serveProduct('WATER');
        $this->assertEquals([25, 10], $machine->getChange());
    }

    public function testGetProductCode()
    {
        Coin::factory()->create(['value' => 10, 'stock' => 2, 'earned' => 0]);
        Product::factory()->create(['code' => 'WATER', 'stock' => 1, 'price' => 10]);

        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $machine->insertCoin(10);
        $machine->serveProduct('WATER');
        $this->assertEquals('WATER', $machine->getProductCode());
    }

    public function testUpdateStates()
    {
        Coin::factory()->create(['value' => 25, 'stock' => 1, 'earned' => 0]);
        Coin::factory()->create(['value' => 10, 'stock' => 2, 'earned' => 2]);
        Coin::factory()->create(['value' => 5, 'stock' => 6, 'earned' => 0]);
        Product::factory()->create(['code' => 'WATER', 'stock' => 2, 'price' => 80]);

        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $machine->insertCoin(25);
        $machine->insertCoin(25);
        $machine->insertCoin(25);
        $machine->insertCoin(10);
        $machine->serveProduct('WATER');
        $machine->updateStates();

        $this->assertDatabaseHas('coins', ['value' => 25, 'stock' => 1, 'earned' => 3]);
        $this->assertDatabaseHas('coins', ['value' => 10, 'stock' => 2, 'earned' => 3]);
        $this->assertDatabaseHas('coins', ['value' => 5, 'stock' => 5, 'earned' => 0]);
        $this->assertDatabaseHas('products', ['code' => 'WATER', 'stock' => 1, 'price' => 80]);

    }

    public function testInsertCoin()
    {
        Coin::factory()->create(['value' => 100, 'stock' => 2, 'earned' => 0]);

        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $machine->insertCoin(100);
        $this->assertEquals([100], $machine->returnCoins());
        $this->assertEquals([], $machine->returnCoins());
    }

    public function testServeProduct()
    {
        Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 0]);
        Product::factory()->create(['code' => 'WATER', 'stock' => 1, 'price' => 100]);
        Product::factory()->create(['code' => 'JUICE', 'stock' => 1, 'price' => 25]);

        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $machine->insertCoin(25);
        $this->expectException(ProductException::class);
        $machine->serveProduct('WATER');

        $machine->serveProduct('JUICE');
        $this->assertEquals('JUICE', $machine->getProductCode());
    }

    public function testGetCoinsStatus(): void
    {
        Coin::factory()->count(15)->create();

        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $result = $machine->getCoinsStatus();

        $this->assertCount(15, $result);
    }

    public function testGetProductsStatus(): void
    {
        Product::factory()->count(10)->create();

        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $result = $machine->getProductsStatus();

        $this->assertCount(10, $result);

    }

    public function testAddCoins(): void
    {
        Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 0]);
        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $machine->addCoins(25, 5);
        $this->assertDatabaseHas('coins', ['value' => 25, 'stock' => 7, 'earned' => 0]);
    }

    public function testAddProducts(): void
    {
        Product::factory()->create(['code' => 'WATER', 'stock' => 2]);
        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $machine->addProducts('WATER', 5);
        $this->assertDatabaseHas('products', ['code' => 'WATER', 'stock' => 7]);
    }

    public function testCollectCoins(): void
    {
        Coin::factory()->create(['value' => 100, 'stock' => 1, 'earned' => 15]);
        Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 20]);

        $machine = new VendingMachine(new CoinRepository(), new ProductRepository());
        $machine->collectCoins();

        $this->assertDatabaseHas('coins', ['value' => 100, 'stock' => 1, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 25, 'stock' => 2, 'earned' => 0]);
    }
}
