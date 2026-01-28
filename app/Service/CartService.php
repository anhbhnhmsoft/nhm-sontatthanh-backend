<?php

namespace App\Service;

use App\Core\Service\BaseService;
use App\Core\Service\ServiceReturn;
use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService extends BaseService
{
    public function __construct(protected Cart $cart, protected CartItem $cartItem)
    {
        parent::__construct();
    }

    private function getCart($user)
    {
        return $this->cart->firstOrCreate(['user_id' => $user->id]);
    }

    public function list(): ServiceReturn
    {
        try {
            $user = Auth::user();
            $cart = $this->cart->where('user_id', $user->id)->with('items.product')->first();
            return ServiceReturn::success($cart ? ($cart->items->map(function ($item) {
                return [
                    'id' => (string) $item->id, 
                    'quantity' => (int) $item->quantity,
                    'product' => ProductResource::make($item->product)
                ];
            })) : []);
        } catch (\Throwable $th) {
            return ServiceReturn::error($th->getMessage());
        }
    }

    public function add(int $productId, int $quantity): ServiceReturn
    {
        try {
            $user = Auth::user();
            $cart = $this->getCart($user);

            $cartItem = $this->cartItem->where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                $this->cartItem->create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $quantity
                ]);
            }

            return $this->list();
        } catch (\Throwable $th) {
            return ServiceReturn::error($th->getMessage());
        }
    }

    public function update(int $itemId, int $quantity): ServiceReturn
    {
        try {
            $user = Auth::user();
            $cart = $this->cart->where('user_id', $user->id)->first();

            if (!$cart) {
                return ServiceReturn::error('Cart not found');
            }

            $cartItem = $this->cartItem->where('cart_id', $cart->id)
                ->where('id', $itemId)
                ->first();

            if (!$cartItem) {
                return ServiceReturn::error('Item not found in cart');
            }

            $cartItem->quantity = $quantity;
            $cartItem->save();

            return $this->list();
        } catch (\Throwable $th) {
            return ServiceReturn::error($th->getMessage());
        }
    }

    public function remove(int $itemId): ServiceReturn
    {
        try {
            $user = Auth::user();
            $cart = $this->cart->where('user_id', $user->id)->first();

            if ($cart) {
                $this->cartItem->where('cart_id', $cart->id)
                    ->where('id', $itemId)
                    ->delete();
            }

            return $this->list();
        } catch (\Throwable $th) {
            return ServiceReturn::error($th->getMessage());
        }
    }

    public function clear(): ServiceReturn
    {
        try {
            $user = Auth::user();
            $cart = $this->cart->where('user_id', $user->id)->first();

            if ($cart) {
                $cart->items()->delete();
            }

            return $this->list();
        } catch (\Throwable $th) {
            return ServiceReturn::error($th->getMessage());
        }
    }
}
