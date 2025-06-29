<?php

namespace App\Http\Resources;

use App\Models\ProductPrice;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'quantity' => $this->quantity,
            'product_price' => new ProductPriceResource($this->whenLoaded('productPrice')),
            'product_price_id' => $this->product_price_id
        ];
    }
}
