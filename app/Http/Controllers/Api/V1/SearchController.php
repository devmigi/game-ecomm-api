<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class SearchController extends ApiController
{

    /**
     * Display a single product details.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($key)
    {
        $products = Product::where('keywords', 'like', "%{$key}%")->with(['images.image', 'attributes', 'category'])->get();

        return $this->sendResponse(ProductResource::collection($products), 'search results');
    }
}
