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
     * Add coin to counter stack
     * @param $value
     * @throws CoinException
     */
    public function insertCoin($value): void
    {
        $this->counter->insertCoin($value);
    }

    /**
     * Return the selected product code on dispenser
     * @return string
     * @throws ProductException
     */
    public function getProductCode(): string
    {
        return $this->dispenser->getProductCode();
    }

    /**
     * Return the coin values to serve the change
     * @return array
     */
    public function getChange(): array
    {
        return $this->counter->getChange();
    }

    /**
     * Return the inserted coins values and empty the inserted stack
     * @return array
     */
    public function returnCoins(): array
    {
        return $this->counter->returnCoins();
    }

    /**
     * Return all stored coins on DB
     * @return Collection
     */
    public function getCoinsStatus(): Collection
    {
        return $this->counter->getCoinsStatus();
    }

    /**
     * Return all stored products on DB
     * @return Collection
     */
    public function getProductsStatus(): Collection
    {
        return $this->dispenser->getProductsStatus();
    }

    /**
     * Select the product by code and calculate the necessary change to return
     * trow a Coin exception if it can serve the necessary change
     * trow a Product exceptions if product code is invalid or it have not enough stock
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

    /**
     * Update product stock and coin stock and earned value depends on selected product and calculated change
     * @throws ProductException
     */
    public function updateStates(): void
    {
        $this->counter->updateCoinStatus();
        $this->dispenser->updateProductStock();
    }

    /**
     * Add the quantity to value coin
     * trow a Coin Exception if quantity or coin value are invalid
     * @param int $value
     * @param int $quantity
     * @return bool
     * @throws CoinException
     */
    public function addCoins(int $value, int $quantity): bool
    {
        return $this->counter->addStock($value, $quantity);
    }

    /**
     * Add the quantity to product stock
     * trow a Product Exception if quantity or product code are invalid
     * @param string $code
     * @param int $quantity
     * @return bool
     * @throws ProductException
     */
    public function addProducts(string $code, int $quantity): bool
    {
        return $this->dispenser->addStock($code, $quantity);
    }

    /**
     * Set zero to all coins earned value
     */
    public function collectCoins(): void
    {
        $this->counter->collectCoins();
    }

}