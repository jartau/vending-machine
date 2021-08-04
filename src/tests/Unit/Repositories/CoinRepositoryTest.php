<?php

namespace Tests\Unit\Repositories;

use App\Models\Coin;
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

    public function testUpdate(): void
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

    public function testFindByValue(): void
    {
        Coin::factory()->create([
            'value' => 1,
            'stock' => 10,
            'earned' => 5
        ]);

        $this->assertInstanceOf(Coin::class, $this->repo->findByValue(1));
    }

    public function testAll(): void
    {
        Coin::factory()->count(15)->create();
        $coins = $this->repo->all();
        $this->assertCount(15, $coins);
    }

    public function testAddStockByValue(): void
    {
        Coin::factory()->create(['value' => 1, 'stock' => 10, 'earned' => 5]);
        Coin::factory()->create(['value' => 25, 'stock' => 3, 'earned' => 5]);

        $this->repo->addStockByValue(1, 3);
        $this->assertDatabaseHas('coins', ['value' => 1, 'stock' => 13]);

        $this->assertFalse($this->repo->addStockByValue(15, 7));

        $this->repo->addStockByValue(25, -1);
        $this->assertDatabaseHas('coins', ['value' => 25, 'stock' => 2]);
    }

    public function testUpdateAll(): void
    {
        Coin::factory()->create(['value' => 1, 'stock' => 10, 'earned' => 5]);
        Coin::factory()->create(['value' => 25, 'stock' => 3, 'earned' => 5]);

        $this->repo->updateAll([
            'stock' => 1000,
            'earned' => 1001
        ]);

        $this->assertDatabaseHas('coins', ['value' => 1, 'stock' => 1000, 'earned' => 1001]);
        $this->assertDatabaseHas('coins', ['value' => 25, 'stock' => 1000, 'earned' => 1001]);
    }
}
