<?php

namespace Tests\Models;

use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{

    public function testHasStock()
    {
        $this->assertTrue(Product::factory()->make(['stock' => 1])->hasStock());
        $this->assertTrue(Product::factory()->make(['stock' => 10])->hasStock());
        $this->assertFalse(Product::factory()->make(['stock' => 0])->hasStock());
        $this->assertFalse(Product::factory()->make(['stock' => -1])->hasStock());
    }
}
