<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends ApiController
{
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    /**
     * Display a listing of products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $products = $this->productRepository->all();

        return $this->sendResponse(ProductResource::collection($products), 'all products');
    }


    /**
     * Display a single product details.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Cache::rememberForever("api.products.{$id}", function () {
            return Product::with(['images.image', 'attributes', 'category'])->first();
        });

        return $this->sendResponse(new ProductResource($product), 'product details');
    }
}
