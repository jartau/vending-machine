<?php


namespace App\Repositories;


use App\Models\Coin;
use Illuminate\Support\Collection;

class CoinRepository implements CoinRepositoryInterface
{

    public function all(): Collection
    {
        return Coin::all();
    }

    public function update(int $id, array $attributes): bool
    {
        return Coin::find($id)->firstOrFail()->update($attributes);
    }

    public function findByValue(float $value): Coin
    {
        return Coin::where('value', $value)->firstOrFail();
    }

}