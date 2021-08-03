<?php


namespace App\Repositories;


use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{

    /**
     * Return all stored products in DB ordered by order
     * @param string $order
     * @return Collection
     */
    public function all(string $order = 'price'): Collection
    {
        return Product::orderByDesc($order)->get();
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

    /**
     * Increment the stock quantity of code product
     * @param string $code
     * @param int $quantity
     * @return bool
     */
    public function addStockByCode(string $code, int $quantity): bool
    {
        return Product::where('code', $code)->update(['stock' => DB::raw('stock + ' . $quantity)]);
    }
}