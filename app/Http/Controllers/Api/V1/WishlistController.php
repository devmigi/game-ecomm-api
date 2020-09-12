<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class WishlistController extends ApiController
{

    /**
     * API - Add to Wishlist
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        $user = $request->user();

        if ($validator->fails()) {
            return $this->sendError('Validation failed.',  $validator->errors());
        }
        $input = $request->all();

        $wishlist = Wishlist::where('product_id', $input['product_id'])->where('user_id', $user->id)->first();

        if($wishlist){
            $wishlist->deleted = false;
        }
        else{
            $wishlist = new Wishlist();
            $wishlist->user_id = $user->id;
            $wishlist->product_id = $input['product_id'];
        }
        $saved = $wishlist->save();

        // clear cache
        Cache::forget("users.{$user->id}.wishlists");

        return $this->sendResponse(['added' => $saved], 'Successfully added.');
    }


    /**
     * API - Remove from Wishlist
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function remove(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        $user = $request->user();

        if ($validator->fails()) {
            return $this->sendError('Validation failed.',  $validator->errors());
        }

        $input = $request->all();

        $updated = Wishlist::where('product_id', $input['product_id'])->where('user_id', $user->id)->update(['deleted' => true]);

        // clear cache
        Cache::forget("users.{$user->id}.wishlists");

        return $this->sendResponse(['updated' => $updated], 'Successfully removed.');
    }


    /**
     * API - Get user's Wishlist
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function all(Request $request){

        $products = Cache::rememberForever("users.{$request->user()->id}.wishlists", function () use ($request) {
            $productIds = $request->user()->wishlists()->where('deleted', false)->pluck('product_id');

            return Product::whereIn('id', $productIds)->with(['images.image', 'attributes', 'category'])->get();
        });

        return $this->sendResponse(['products' => ProductResource::collection($products)], 'user wishlists.');
    }

}
