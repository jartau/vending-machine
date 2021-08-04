<?php


namespace App\Repositories;


use App\Models\Coin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CoinRepository implements CoinRepositoryInterface
{

    /**
     * Return all stored coins ordered by order
     * @param string $order
     * @return Collection
     */
    public function all(string $order = 'value'): Collection
    {
        return Coin::orderBy($order)->get();
    }

    /**
     * Update the attributes of coin with id
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool
    {
        return Coin::find($id)->update($attributes);
    }

    /**
     * Return the coin by value
     * If product not exist trow a ModelNotFoundException
     * @param int $value
     * @return Coin
     */
    public function findByValue(int $value): Coin
    {
        return Coin::where('value', $value)->firstOrFail();
    }

    public function addStockByValue(int $value, int $quantity): bool
    {
        return Coin::where('value', $value)->update(['stock' => DB::raw('stock + ' . $quantity)]);
    }

    public function updateAll(array $attributes): void
    {
        Coin::query()->update($attributes);
    }

}