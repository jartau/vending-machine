<?php


namespace App\Repositories;


use App\Models\Coin;

interface CoinRepositoryInterface extends BaseRepositoryInterface
{

    public function findByValue(int $value): Coin;

    public function addStockByValue(int $value, int $quantity): bool;

    public function updateAll(array $attributes): void;

}