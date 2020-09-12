<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;

class PageController extends ApiController
{
    private $productRepository;
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function load($page)
    {
        $response = Page::where('name', $page)->with('sections.items.image')->first();

        return $this->sendResponse(new PageResource($response), 'page data');
    }

}
