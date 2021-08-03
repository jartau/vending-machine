<?php


namespace App\Repositories;


use App\Models\Coin;
use Illuminate\Support\Collection;

class CoinRepository implements CoinRepositoryInterface
{

    /**
     * Return all stored coins
     * @return Collection
     */
    public function all(): Collection
    {
        return Coin::all();
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
     * @param float $value
     * @return Coin
     */
    public function findByValue(float $value): Coin
    {
        return Coin::where('value', $value)->firstOrFail();
    }

}