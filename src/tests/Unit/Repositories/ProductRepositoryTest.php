<?php

namespace Tests\Unit\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{

    use RefreshDatabase;

    private ProductRepository $repo;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->repo = new ProductRepository();
    }

    public function testAll()
    {
        Product::factory()->count(5)->create();
        $this->assertCount(5, $this->repo->all());
    }

    public function testUpdate()
    {
        $prod = Product::factory()->create([
            'price' => 1,
            'stock' => 34
        ]);

        $this->repo->update($prod->id, [
            'price' => 2,
            'stock' => 5
        ]);
        $this->assertDatabaseHas('products', ['id' => $prod->id, 'price' => 2, 'stock' => 5]);

    }

    public function testAddStockByCode()
    {
        Product::factory()->create([
            'code' => 'WATER',
            'price' => 1,
            'stock' => 34
        ]);

        Product::factory()->create([
            'code' => 'JUICE',
            'price' => 1,
            'stock' => 5
        ]);

        $this->repo->addStockByCode('WATER', 3);
        $this->assertDatabaseHas('products', ['code' => 'WATER', 'stock' => 37]);

        $this->repo->addStockByCode('JUICE', -1);
        $this->assertDatabaseHas('products', ['code' => 'JUICE', 'stock' => 4]);

        $this->assertFalse($this->repo->addStockByCode('MILK', 1));

    }

}
