<?php


namespace App\VendingMachine;


use App\Exceptions\CoinException;
use App\Exceptions\ProductException;
use App\Exceptions\VendingMachineException;
use App\Repositories\CoinRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Collection;

class VendingMachine
{
    private CoinCounter $counter;
    private ProductDispenser $dispenser;

    public function __construct(CoinRepository $coinRepository, ProductRepository $productRepository)
    {
        $this->counter = new CoinCounter($coinRepository);
        $this->dispenser = new ProductDispenser($productRepository);
    }

    /**
     * @param $value
     * @throws CoinException
     */
    public function insertCoin($value): void
    {
        $this->counter->insertCoin($value);
    }

    public function getProductCode(): string
    {
        return $this->dispenser->getProductCode();
    }

    public function getChange(): array
    {
        return $this->counter->getChange();
    }

    public function returnCoins(): array
    {
        return $this->counter->returnCoins();
    }

    public function getCoinsStatus(): Collection
    {
        return $this->counter->getCoinsStatus();
    }

    public function getProductsStatus(): Collection
    {
        return $this->dispenser->getProductsStatus();
    }

    /**
     * @param $code
     * @throws CoinException
     * @throws ProductException
     */
    public function serveProduct($code)
    {
        $this->dispenser->chooseProduct($code);

        if ($this->counter->getInsertedAmount() < $this->dispenser->getProductPrice()) {
            throw new ProductException('Not enough money', 400);
        }

        $this->counter->calcChange($this->dispenser->getProductPrice());

    }

    public function updateStates(): void
    {
        $this->counter->updateCoinStatus();
        $this->dispenser->updateProductStock();
    }

    public function addCoins(int $value, int $quantity): bool
    {
        return $this->counter->addStock($value, $quantity);
    }

    public function addProducts(string $code, int $quantity): bool
    {
        return $this->dispenser->addStock($code, $quantity);
    }

    public function collectCoins(): void
    {
        $this->counter->collectCoins();
    }

}