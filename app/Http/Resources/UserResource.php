<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
            'wishlist' => WishlistResource::collection($this->whenLoaded('wishlist')),
        ];
    }
}
