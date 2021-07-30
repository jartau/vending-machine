<?php


namespace App\Repositories;


use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{

    public function all(): Collection
    {
        return Product::all();
    }

    public function update(int $id, array $attributes): bool
    {
        return Product::find($id)->firstOrFail()->update($attributes);
    }

    public function findByCode(string $code): ?Model
    {
        return Product::where('code', $code)->firstOrFail();
    }

}