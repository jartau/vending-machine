<?php


namespace App\VendingMachine;


use App\Exceptions\CoinException;
use App\Helpers\SessionStack;
use App\Models\Coin;
use App\Repositories\CoinRepository;
use App\Repositories\CoinRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class CoinCounter
{
    /**
     * Session key name
     */
    private const SESSION_KEY = 'inserted_money';

    /**
     * @var CoinRepository|CoinRepositoryInterface
     */
    private CoinRepository $coinRepository;

    /**
     * @var array coin values for serve change
     */
    private array $change;

    private SessionStack $coinStack;

    /**
     * CoinCounter constructor.
     * @param CoinRepositoryInterface $coinRepository
     */
    public function __construct(CoinRepositoryInterface $coinRepository)
    {
        $this->coinStack = new SessionStack(self::SESSION_KEY);
        $this->coinRepository = $coinRepository;
        $this->change = [];
    }


    /**
     * Reset change and inserted coins lists
     */
    private function resetCoins(): void
    {
        $this->change = [];
        $this->coinStack->reset();
    }

    /**
     * Return the total inserted coins amount
     * @return int
     */
    public function getInsertedAmount(): int
    {
        $amount = 0;
        foreach ($this->coinStack->get() as $value) {
            $amount += $value;
        }
        return $amount;
    }

    /**
     * Return the change array list
     * @return array
     */
    public function getChange(): array
    {
        return $this->change;
    }

    /**
     * Add the coin value into interted coins list
     * If the money value of coin not exists in DB throws a CoinException
     * @param int $value
     * @throws CoinException
     */
    public function insertCoin(int $value): void
    {
        try {
            $this->coinRepository->findByValue($value);
        } catch (ModelNotFoundException $e) {
            throw new CoinException('Invalid coin value', 404);
        }
        $this->coinStack->push($value);

    }

    /**
     * Return the values of inserted coins and reset change and inserted coins lists
     * @return array
     */
    public function returnCoins(): array
    {
        $coins = $this->coinStack->get();
        $this->resetCoins();
        return $coins;
    }

    /**
     * Calculates the coins needed to return the change
     * If there are not enough coins trow a CoinException
     *
     * @param int $price
     * @throws CoinException
     */
    public function calcChange(int $price): void
    {
        $debt = $this->getInsertedAmount() - $price;
        $coins = $this->coinRepository->all()->sortBy('value', SORT_REGULAR, true);
        $coins->each(function (Coin $coin) use (&$debt) {
            if ($debt == 0) { return false; }

            while ($coin->stock > 0 && ($debt - $coin->value) >= 0) {
                $debt -= $coin->value;
                $coin->stock--;
                array_push($this->change, $coin->value);
            }

        });

        if ($debt > 0) {
            throw new CoinException('There are not enough coins to serve change', 400);
        }
    }


    /**
     * updates stock and earned coins based on inserted coins and change
     */
    public function updateCoinStatus(): void
    {
        $newStock = array_count_values($this->getChange());
        $newEarned = array_count_values($this->coinStack->get());

        $this->coinRepository->all()->each(function(Coin $coin) use ($newStock, $newEarned) {
            $attributes = [];
            if (array_key_exists($coin->value, $newStock)) {
                $attributes['stock'] = $coin->stock - $newStock[$coin->value];
            }
            if (array_key_exists($coin->value, $newEarned)) {
                $attributes['earned'] = $coin->earned + $newEarned[$coin->value];
            }
            if (count($attributes) > 0) {
                $this->coinRepository->update($coin->id, $attributes);
            }
        });

        $this->resetCoins();
    }

    public function getCoinsStatus(): Collection
    {
        return $this->coinRepository->all();
    }

    /**
     * @param int $value
     * @param int $quantity
     * @return bool
     * @throws CoinException
     */
    public function addStock(int $value, int $quantity): bool
    {
        try {
            $coin = $this->coinRepository->findByValue($value);
        } catch (ModelNotFoundException $e) {
            throw new CoinException('Invalid coin value', 404);
        }

        if ($coin->stock + $quantity < 0) {
            throw new CoinException('Not enough coins', 400);
        }
        return $this->coinRepository->addStockByValue($value, $quantity);
    }

    public function collectCoins(): void
    {
        $this->coinRepository->updateAll(['earned' => 0]);
    }
}