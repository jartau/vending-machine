<?php

namespace Tests\Unit\VendingMachine;

use App\Exceptions\CoinException;
use App\Models\Coin;
use App\Repositories\CoinRepository;
use App\VendingMachine\CoinCounter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoinCounterTest extends TestCase
{

    use RefreshDatabase;

    public function providerServeChange(): array
    {
        return [
            [ 150,      [100, 100, 25],         [25, 10, 10, 5, 5, 5, 5 ,5, 5]          ],
            [ 200,      [100, 100],             []                                      ],
            [ 65,       [25, 25, 25],           [10]                                    ],
            [ 30,       [100, 100, 100],        [100, 100, 25, 10, 10, 5, 5 ,5 ,5 ,5]   ]
        ];
    }

    /**
     * @param float $price
     * @param array $insertedAmount
     * @param $result
     * @throws CoinException
     * @dataProvider providerServeChange
     */
    public function testServeChange(float $price, array $insertedAmount, $result): void
    {
        Coin::factory()->create(['value' => 100, 'stock' => 2, 'earned' => 0]);
        Coin::factory()->create(['value' => 25, 'stock' => 1, 'earned' => 0]);
        Coin::factory()->create(['value' => 10, 'stock' => 2, 'earned' => 0]);
        Coin::factory()->create(['value' => 5, 'stock' => 6, 'earned' => 0]);

        $counter = new CoinCounter(new CoinRepository());
        foreach ($insertedAmount as $inserted) {
            $counter->insertCoin($inserted);
        }
        $counter->calcChange($price);
        $this->assertEquals($result, $counter->getChange());
    }


    /**
     * @throws CoinException
     */
    public function testUpdateCoinStatus(): void
    {
        Coin::factory()->create(['value' => 100, 'stock' => 2, 'earned' => 0]);
        Coin::factory()->create(['value' => 25, 'stock' => 1, 'earned' => 0]);
        Coin::factory()->create(['value' => 10, 'stock' => 2, 'earned' => 0]);
        Coin::factory()->create(['value' => 5, 'stock' => 6, 'earned' => 0]);

        $counter = new CoinCounter(new CoinRepository());
        $counter->insertCoin(100);
        $counter->insertCoin(25);
        $counter->calcChange(105);
        $counter->updateCoinStatus();
        $this->assertDatabaseHas('coins', ['value' => 100, 'stock' => 2, 'earned' => 1]);
        $this->assertDatabaseHas('coins', ['value' => 25, 'stock' => 1, 'earned' => 1]);
        $this->assertDatabaseHas('coins', ['value' => 10, 'stock' => 0, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 5, 'stock' => 6, 'earned' => 0]);
    }

    /**
     * @throws CoinException
     */
    public function testServeChangeNotEnoughCoins(): void
    {
        Coin::factory()->create(['value' => 100, 'stock' => 0, 'earned' => 0]);
        Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 0]);

        $counter = new CoinCounter(new CoinRepository());
        $counter->insertCoin(100);

        $this->expectException(CoinException::class);
        $r = $counter->calcChange(45);
    }

    /**
     * @throws CoinException
     */
    public function testInsertCoin(): void
    {
        Coin::factory()->create(['value' => 100, 'stock' => 0, 'earned' => 0]);
        Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 0]);

        $counter = new CoinCounter(new CoinRepository());

        $this->expectException(CoinException::class);
        $counter->insertCoin(100);
        $counter->insertCoin(25);

        $this->expectException(CoinException::class);
        $counter->insertCoin(200);
    }

    /**
     * @throws CoinException
     */
    public function testGetInsertedAmount(): void
    {
        Coin::factory()->create(['value' => 100, 'stock' => 0, 'earned' => 0]);
        Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 0]);

        $counter = new CoinCounter(new CoinRepository());
        $counter->insertCoin(100);
        $counter->insertCoin(25);

        $this->assertEquals(125, $counter->getInsertedAmount());
    }

    /**
     * @throws CoinException
     */
    public function testReturnCoins(): void
    {
        Coin::factory()->create(['value' => 100, 'stock' => 0, 'earned' => 0]);
        Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 0]);

        $counter = new CoinCounter(new CoinRepository());

        $this->assertEquals([], $counter->returnCoins());

        $counter->insertCoin(100);
        $counter->insertCoin(25);
        $counter->insertCoin(100);

        $this->assertEquals([100, 25, 100], $counter->returnCoins());
        $this->assertEquals([], $counter->returnCoins());
    }

    public function testGetCoinsStatus(): void
    {
        $coin1 = Coin::factory()->create(['value' => 100, 'stock' => 0, 'earned' => 0]);
        $coin2 = Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 0]);

        $counter = new CoinCounter(new CoinRepository());
        $result = $counter->getCoinsStatus();
        $this->assertTrue($result->contains($coin1));
        $this->assertTrue($result->contains($coin2));
        $this->assertCount(2, $result);

    }

    /**
     * @throws CoinException
     */
    public function testAddStock(): void
    {
        Coin::factory()->create(['value' => 100, 'stock' => 1, 'earned' => 0]);
        Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 0]);

        $counter = new CoinCounter(new CoinRepository());

        $counter->addStock(100, 3);
        $this->assertDatabaseHas('coins', ['value' => 100, 'stock' => 4, 'earned' => 0]);
        $this->expectException(CoinException::class);
        $counter->addStock(25, -3);

        $this->expectException(CoinException::class);
        $counter->addStock(33, 5);
    }

    public function testCollectCoins(): void
    {
        Coin::factory()->create(['value' => 100, 'stock' => 1, 'earned' => 15]);
        Coin::factory()->create(['value' => 25, 'stock' => 2, 'earned' => 20]);

        $counter = new CoinCounter(new CoinRepository());
        $counter->collectCoins();

        $this->assertDatabaseHas('coins', ['value' => 100, 'stock' => 1, 'earned' => 0]);
        $this->assertDatabaseHas('coins', ['value' => 25, 'stock' => 2, 'earned' => 0]);
    }
}
