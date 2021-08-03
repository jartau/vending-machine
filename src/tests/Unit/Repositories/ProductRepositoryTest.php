<?php

namespace Tests\Repositories;

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

        $this->assertEquals(1 , Product::where('id', $prod->id)->where('price', 2)->where('stock', 5)->count());
    }

}
