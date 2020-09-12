<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(){
        $products = Cache::rememberForever('products.all', function () {
            return Product::with(['images.image', 'attributes', 'category'])->get();
        });

        return $products;
    }


    public function getById($id){
        $product = Cache::rememberForever("products.{$id}", function () use ($id) {
            return Product::where('id', $id)->with(['images.image', 'attributes', 'category'])->first();
        });

        return $product;
    }
}