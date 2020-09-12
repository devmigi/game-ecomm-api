<?php

namespace App\Http\Resources;

use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    private $productRepository;
    private $categoryRepository;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->productRepository = new ProductRepository();
        $this->categoryRepository = new CategoryRepository();

        $page = [
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'sections' => $this->sections->map(function ($section, $key) {
                return [
                    'type' => $section->type,
                    'title' => $section->title,
                    'description' => $section->description,
                    'active' => $section->active,
                    'items' => $section->items->map(function ($item, $key) {
                        $data = null;
                        if($item->item_type == 'product'){
                            $product = $this->productRepository->getById($item->item_id);
                            $data = new ProductResource($product);
                        }
                        if($item->item_type == 'category'){
                            $category = $this->categoryRepository->getById($item->item_id);
                            $data = new CategoryResource($category);
                        }

                        return [
                            'item_type' => $item->item_type,
                            'item_id' => $item->item_id,
                            'image' => ($item->image) ? $item->image->web_url : null,
                            'title' => $item->title,
                            'subtitle' => $item->subtitle,
                            'data' => $data,
                            'active' => $this->active,
                        ];
                    }),
                ];
            }),
        ];

        return $page;
    }
}
