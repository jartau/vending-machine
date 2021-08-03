<?php

namespace App\Http\Controllers;

use App\Exceptions\CoinException;
use App\Exceptions\ProductException;
use App\Helpers\Money;
use App\Repositories\CoinRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use App\VendingMachine\VendingMachine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    private VendingMachine $vendingMachine;

    public function __construct(CoinRepositoryInterface $coinRepository, ProductRepositoryInterface $productRepository)
    {
        $this->vendingMachine = new VendingMachine($coinRepository, $productRepository);
    }

    public function insertCoin(Request $request): JsonResponse
    {
        try {
            $amount = Money::toInternalMoney($request->get('value'));
            $this->vendingMachine->insertCoin($amount);
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

    public function chooseProduct(Request $request): JsonResponse
    {
        try {
            $this->vendingMachine->serveProduct($request->get('code'));
            $productCode = $this->vendingMachine->getProductCode();
            $change = $this->vendingMachine->getChange();
            $this->vendingMachine->updateStates();

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $productCode,
                    'exchange' => array_map([Money::class, 'format'], $change)
                ]
            ]);
        } catch (CoinException | ProductException $e) {
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

    public function returnCoin(): JsonResponse
    {
        try {
            $coins = $this->vendingMachine->returnCoins();
            return response()->json([
                'success' => true,
                'data' => [
                    'product' => null,
                    'exchange' => array_map([Money::class, 'format'], $coins)
                ]
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
}
