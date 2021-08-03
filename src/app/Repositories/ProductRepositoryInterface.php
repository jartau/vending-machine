<?php


namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{

    public function findByCode(string $code): ?Model;

    public function addStockByCode(string $code, int $quantity): bool;

}