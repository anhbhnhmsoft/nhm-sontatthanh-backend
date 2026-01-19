<?php

namespace App\Service;

use App\Core\Service\BaseService;
use App\Core\Service\ServiceReturn;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistService extends BaseService
{

    public function __construct(protected Wishlist $wishlist)
    {
        parent::__construct();
    }
    /**
     * Get wishlist items
     * @return ServiceReturn
     */
    public function list(): ServiceReturn
    {
        try {
            $user = Auth::user();
            $wishlist = $this->wishlist->where('user_id', $user->id)
                ->with('product')
                ->get();
            return ServiceReturn::success($wishlist);
        } catch (\Throwable $th) {
            return ServiceReturn::error(
                message: $th->getMessage(),
            );
        }
    }

    /**
     * Add wishlist items
     * @return ServiceReturn
     */
    public function add(array $data): ServiceReturn
    {
        try {
            $user = Auth::user();
            foreach($data as $item){
                if($this->wishlist->where('user_id', $user->id)
                    ->where('product_id', $item)
                    ->exists()){
                    continue;
                }
                $this->wishlist->create([
                    'user_id' => $user->id,
                    'product_id' => $item,
                ]);
            }
            return ServiceReturn::success();
        } catch (\Throwable $th) {
            return ServiceReturn::error(
                message: $th->getMessage(),
            );
        }
    }

    /**
     * Remove wishlist items
     * @return ServiceReturn
     */
    public function remove(array $data): ServiceReturn
    {
        try {
            $user = Auth::user();
            foreach($data as $item){
                $this->wishlist->where('user_id', $user->id)
                    ->where('product_id', $item)
                    ->delete();
            }
            return ServiceReturn::success();
        } catch (\Throwable $th) {
            return ServiceReturn::error(
                message: $th->getMessage(),
            );
        }
    }
}
