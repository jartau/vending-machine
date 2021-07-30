<?php


namespace App\Repositories;


use App\Models\Coin;

interface CoinRepositoryInterface extends BaseRepositoryInterface
{
    public function findByValue(float $value): Coin;
}