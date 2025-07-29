<?php

namespace App\Http\Resources;

use App\Models\ProductPrice;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'product_price_id' => $this->product_price_id,
            'product_price_name' => $this->product_price_name,
            'product_sub_text' => $this->product_sub_text,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'original_unit_price' => $this->original_unit_price,
            'total' => $this->total,
        ];
    }
}
