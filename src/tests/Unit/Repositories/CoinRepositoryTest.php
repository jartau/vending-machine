<?php

namespace Tests\Unit\Repositories;

use App\Models\Coin;
use App\Models\Product;
use App\Repositories\CoinRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoinRepositoryTest extends TestCase
{

    use RefreshDatabase;

    private CoinRepository $repo;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->repo = new CoinRepository();
    }

    public function testUpdate()
    {
        $coin = Coin::factory()->create([
            'value' => 1,
            'stock' => 10,
            'earned' => 5
        ]);

        $this->assertTrue($this->repo->update($coin->id, [
            'stock' => 15,
            'earned' => 7
        ]));

        $this->assertEquals(1 , Coin::where('id', $coin->id)->where('stock', 15)->where('earned', 7)->count());
    }

    public function testFindByValue()
    {
        Coin::factory()->create([
            'value' => 1,
            'stock' => 10,
            'earned' => 5
        ]);

        $this->assertInstanceOf(Coin::class, $this->repo->findByValue(1));
    }

    public function testAll()
    {
        Coin::factory()->count(15)->create();
        $coins = $this->repo->all();
        $this->assertCount(15, $coins);
    }
}
