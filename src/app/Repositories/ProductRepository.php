<?php


namespace App\Repositories;


use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{

    /**
     * Return all stored products in DB
     * @return Collection
     */
    public function all(): Collection
    {
        return Product::all();
    }

    /**
     * Update the attributes of product with id
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool
    {
        return Product::find($id)->update($attributes);
    }

    /**
     * Return the product with code
     * @param string $code
     * @return Model|null
     */
    public function findByCode(string $code): ?Model
    {
        return Product::where('code', $code)->firstOrFail();
    }

}