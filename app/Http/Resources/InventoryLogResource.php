<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class InventoryLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $product = Product::where('vendor_id', $this->vendor_id)->where('id', $this->product_id)->first();
        $order = Order::where('id', $this->order_id)->first();

        return [
            'id' => $this->id,
            'date' => $this->date,
            'product_name' => $product->name,
            'product_id' => $this->product_id,
            'type' => $this->type,
            'qty' => $this->qty,
            'amount' => $this->amount,
            'paid' => $this->paid,
            'unit_price' => $this->unit_price,
            'order_id' => $this->order_id,
            'order_no' => $order->order_no ?? ""
        ];
    }
}
