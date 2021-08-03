<?php


namespace App\VendingMachine;


use App\Models\Product;

class ControlPanel
{
    private ProductDispender $dispender;

    public function __construct()
    {
        $this->dispender = new ProductDispender();
    }

    public function insertCoin($value): void
    {
        CoinCounter::insertCoin($value);
    }

    public function chooseProduct($code): Product
    {

    }
}