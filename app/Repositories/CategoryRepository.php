<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(){
        $categories = Cache::rememberForever('categories.all', function () {
            return Category::with('image')->get();
        });

        return $categories;
    }


    public function getById($id){
        $category = Cache::rememberForever("categories.{$id}", function () use ($id) {
            return Category::where('id', $id)->with('image')->first();
        });

        return $category;
    }

    /**
     * Get Category Products
     * @param $id
     * @return mixed
     */
    public function products($id){
        $childCategories = Category::where('parent_id', $id)->pluck('id')->toArray();

        if(is_array($childCategories)){
            $childCategories[] = $id;
        }
        else{
            $childCategories = [$id];
        }

        $categoryProducts = Cache::rememberForever("categories.{$id}.products", function () use ($childCategories) {
            return Product::whereIn('category_id', $childCategories)->with(['images.image', 'attributes', 'category'])->get();
        });

        return $categoryProducts;
    }
}