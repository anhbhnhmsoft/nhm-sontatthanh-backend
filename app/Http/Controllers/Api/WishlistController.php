<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller\BaseController;
use App\Http\Resources\ProductResource;
use App\Service\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends BaseController
{
    public function __construct(
        protected WishlistService $wishlistService,
    )
    {
    }

    /**
     * Get wishlist items
     *
     * @return JsonResponse
     */
    public function list() : JsonResponse
    {
        $wishlist = $this->wishlistService->list();
        if($wishlist->isError()){
            return $this->sendError(
                message: $wishlist->getMessage(),
            );
        }
        $data = $wishlist->getData();
        $products = $data->pluck('product');
        return $this->sendSuccess(
            data: ProductResource::collection($products),
        );
    }

    /**
     * Add wishlist items
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request ) : JsonResponse
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ],[
            'product_id.required' => 'Vui lòng chọn sản phẩm',
            'product_id.exists' => 'Sản phẩm không tồn tại',
        ]);

        if($validate->fails()){
            return $this->sendError(
                message: $validate->errors()->first(),
            );
        }

        $wishlist = $this->wishlistService->add([$validate->validated()['product_id']]);
        if($wishlist->isError()){
            return $this->sendError(
                message: $wishlist->getMessage(),
            );
        }
        $data = $wishlist->getData();
        return $this->sendSuccess(
            data: [
            ],
             message: 'Thêm sản phẩm vào wishlist thành công',
        );
    }
    /**
     * Remove wishlist items
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function remove(Request $request ) : JsonResponse
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ],[
            'product_id.required' => 'Vui lòng chọn sản phẩm',
            'product_id.exists' => 'Sản phẩm không tồn tại',
        ]);

        if($validate->fails()){
            return $this->sendError(
                message: $validate->errors()->first(),
            );
        }

        $wishlist = $this->wishlistService->remove([$validate->validated()['product_id']]);
        if($wishlist->isError()){
            return $this->sendError(
                message: $wishlist->getMessage(),
            );
        }
        $data = $wishlist->getData();
        return $this->sendSuccess(
            data: [
            ],
             message: 'Xóa sản phẩm khỏi wishlist thành công',
        );
    }
}
