<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    private $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->categoryRepository->all();

        return $this->sendResponse(CategoryResource::collection($categories), 'all categories');
    }


    /**
     * Display Category Products.
     *
     * @return \Illuminate\Http\Response
     */
    public function products($id)
    {
        $categoryProducts = $this->categoryRepository->products($id);

        return $this->sendResponse(ProductResource::collection($categoryProducts), 'category products');
    }
}
