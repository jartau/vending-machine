<?php


namespace App\VendingMachine;


use App\Exceptions\CoinException;
use App\Models\Coin;
use App\Repositories\CoinRepository;
use App\Repositories\CoinRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /**
     * CoinCounter constructor.
     * @param CoinRepositoryInterface $coinRepository
     */
    public function __construct(CoinRepositoryInterface $coinRepository)
    {
        $this->coinRepository = $coinRepository;
        $this->change = [];
    }

    /**
     * Set the array coins into session
     * @param array $coins
     */
    private function setInsertedCoins(array $coins): void
    {
        session([self::SESSION_KEY => $coins]);
    }

    /**
     * Reset change and inserted coins lists
     */
    private function resetCoins(): void
    {
        $this->change = [];
        $this->setInsertedCoins([]);
    }

    /**
     * Return the inserted coins list as array
     * @return array
     */
    public function getInsertedCoins(): array
    {
        return session(self::SESSION_KEY, []);
    }

    /**
     * Return the total inserted coins amount
     * @return int
     */
    public function getInsertedAmount(): int
    {
        $amount = 0;
        foreach ($this->getInsertedCoins() as $value) {
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
        $coins = $this->getInsertedCoins();
        array_push($coins, $value);
        $this->setInsertedCoins($coins);
    }

    /**
     * Return the values of inserted coins and reset change and inserted coins lists
     * @return array
     */
    public function returnCoins(): array
    {
        $coins = $this->getInsertedCoins();
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
        $newEarned = array_count_values($this->getInsertedCoins());

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
}