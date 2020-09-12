<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Version;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->parent_id){
            $productVersions = Product::where('parent_id', $this->parent_id)->orWhere('id', $this->parent_id)->with('version')->get();
        }
        else{
            $productVersions = Product::where('parent_id', $this->id)->orWhere('id', $this->id)->with('version')->get();
        }


        $product = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'rating' => $this->rating,
            'details' => $this->details,
            'versions' => $productVersions->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'version' => $item->version->name
                ];
            }),
            'category' => new CategoryResource($this->category),
            'mrp' => $this->mrp,
            'selling_price' => $this->selling_price,
            'trading_price' => $this->trading_price,
            'inventory' => $this->inventory,
            'available_from' => $this->available_from,
            'images' => $this->images->pluck('image.web_url'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];

        return $product;
    }
}
