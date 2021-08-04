<?php

namespace App\Http\Controllers;

use App\Exceptions\CoinException;
use App\Exceptions\ProductException;
use App\Helpers\Money;
use App\Models\Coin;
use App\Models\Product;
use App\Repositories\CoinRepository;
use App\Repositories\CoinRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\VendingMachine\VendingMachine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    private CoinRepository $coinRepository;
    private ProductRepository $productRepository;
    private VendingMachine $vendingMachine;

    public function __construct(CoinRepositoryInterface $coinRepository, ProductRepositoryInterface $productRepository)
    {
        $this->coinRepository = $coinRepository;
        $this->productRepository = $productRepository;
        $this->vendingMachine = new VendingMachine($coinRepository, $productRepository);
    }

    public function info(): JsonResponse
    {
        try {

            $productsInfo = $this->vendingMachine->getProductsStatus()->map(function (Product $product) {
                return [
                    'code' => $product->code,
                    'stock' => $product->stock,
                ];
            })->toArray();

            $coinsInfo = $this->vendingMachine->getCoinsStatus()->map(function (Coin $coin) {
                return [
                    'value' => Money::format($coin->value),
                    'stock' => $coin->stock,
                    'earned' => $coin->earned,
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'product_stock' => $productsInfo,
                    'coin_status' => $coinsInfo
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error'
            ], 500);
        }

    }

    public function addProduct(Request $request): JsonResponse
    {
        try {

            $code = $request->get('code');
            $quantity = $request->get('quantity');

            $this->vendingMachine->addProducts($code, $quantity);

            return response()->json([
                'success' => true,
                'data' => []
            ]);

        } catch (ProductException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error'
            ], 500);
        }
    }

    public function addCoin(Request $request): JsonResponse
    {
        try {

            $value = Money::toInternalMoney($request->get('value'));
            $quantity = $request->get('quantity');

            $this->vendingMachine->addCoins($value, $quantity);

            return response()->json([
                'success' => true,
                'data' => []
            ]);

        } catch (CoinException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error'
            ], 500);
        }
    }

    public function collectCoins(): JsonResponse
    {
        try {
            $this->vendingMachine->collectCoins();
            return response()->json([
                'success' => true,
                'data' => []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error'
            ], 500);
        }
    }
}
