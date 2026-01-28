<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller\BaseController;
use App\Service\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    public function __construct(protected CartService $cartService) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->cartService->list();

        if ($result->isError()) {
            return $this->sendError($result->getMessage());
        }

        return $this->sendSuccess(['data' => $result->getData()]);
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1'
        ]);

        $result = $this->cartService->add(
            $request->product_id,
            $request->input('quantity', 1)
        );

        if ($result->isError()) {
            return $this->sendError($result->getMessage());
        }

        return $this->sendSuccess(['data' => $result->getData()]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $result = $this->cartService->update(
            $request->item_id,
            $request->quantity
        );

        if ($result->isError()) {
            return $this->sendError($result->getMessage(), 400);
        }

        return $this->sendSuccess(['data' => $result->getData()]);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required'
        ]);

        $result = $this->cartService->remove($request->item_id);

        if ($result->isError()) {
            return $this->sendError($result->getMessage());
        }

        return $this->sendSuccess(['data' => $result->getData()]);
    }

    public function clear(Request $request): JsonResponse
    {
        $result = $this->cartService->clear();

        if ($result->isError()) {
            return $this->sendError($result->getMessage());
        }

        return $this->sendSuccess(['data' => $result->getData()]);
    }
}
