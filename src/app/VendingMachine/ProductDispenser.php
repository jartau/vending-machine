<?php


namespace App\VendingMachine;


use App\Exceptions\ProductException;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductDispenser
{
    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @var Product|null Current selected product
     */
    private ?Product $selectedProduct;

    /**
     * ProductDispenser constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->selectedProduct = null;
    }

    /**
     * Return the current selected product price
     * @return int
     * @throws ProductException
     */
    public function getProductPrice(): int
    {
        if ($this->selectedProduct) {
            return $this->selectedProduct->price;
        }
        throw new ProductException('Unselected product', 400);
    }

    /**
     * Return the current selected product code
     * @return string
     * @throws ProductException
     */
    public function getProductCode(): string
    {
        if ($this->selectedProduct) {
            return $this->selectedProduct->code;
        }
        throw new ProductException('Unselected product', 400);
    }

    /**
     * Select a product by code
     * if product not exists or it have not stock trow ProductException
     * @param string $code
     * @throws ProductException
     */
    public function chooseProduct(string $code): void
    {
        try {
            $product = $this->productRepository->findByCode($code);
        } catch (ModelNotFoundException $e) {
            throw new ProductException('Invalid product code', 404);
        }

        if (!$product->hasStock()) {
            throw new ProductException('Unavailable product', 400);
        }

        $this->selectedProduct = $product;
    }

    /**
     * Decrease the current selected product stock and unselect it
     * @throws ProductException
     */
    public function updateProductStock(): void
    {
        if (!$this->selectedProduct) {
            throw new ProductException('Unselected product', 400);
        }
        $this->productRepository->update($this->selectedProduct->id, [
            'stock' => $this->selectedProduct->stock - 1
        ]);
    }

}