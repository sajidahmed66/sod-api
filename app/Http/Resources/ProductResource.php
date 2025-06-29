<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lowPrice = $this->price;
        $highPrice = $this->price;

        if (count($this->prices)) {
            $lowPrice = $this->prices->min('price');
            $highPrice = $this->prices->max('price');
        }

        $priceFormat = '৳'.$lowPrice;

        if ($lowPrice !== $highPrice)
            $priceFormat .= ' - ৳'.$highPrice;

        $prices = $this->prices;
        $minPrice = $this->price;
        $maxPrice = $this->price;
        if (count($prices) > 1) {
            $minPrice = 100000;
            $maxPrice = -1;
            foreach ($prices as $variety) {
                if ($variety->price > $maxPrice) {
                    $maxPrice = $variety->price;
                }
                if ($variety->price < $minPrice) {
                    $minPrice = $variety->price;
                }
            }
        }


        if (count($prices) == 1) {
            foreach ($prices as $variety) {
                $this->price = $variety->price;
                $this->original_price = $variety->original_price;
            }
        }

        return [
            'id' => $this->id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subCategory' => new SubCategoryResource($this->whenLoaded('subCategory')),
            'name' => $this->name,
            'sub_text' => $this->sub_text,
            'description' => $this->description,
            'image' => $this->image ? Storage::url($this->image) : asset('images/no_image.webp'),
            'price' => $this->price,
            'prices' => ProductPriceResource::collection($this->prices),
            'price_format' => $priceFormat,
            'original_price' => $this->original_price,
            'slug' => $this->slug,
            'active' => (bool) $this->active,
            'stock_out' => (bool) $this->stock_out,
            'upcoming_product' => (bool) $this->upcoming_product,
            'wishlist' => $this->wishlist ?: false,
            'range_price' => $minPrice != $maxPrice || count($prices) > 1 ? (integer)$minPrice . ' - ' . '৳'. (integer)$maxPrice : -1
        ];
    }
}
