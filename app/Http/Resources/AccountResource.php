<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AccountResource extends JsonResource
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
            'product_name' => $product->name ?? "",
            'product_id' => $this->product_id,
            'title' => $this->title,
            'type' => $this->type,
            'qty' => $this->qty,
            'unit' => $this->unit,
            'unit_price' => $this->unit_price,
            'amount' => $this->amount,
            'note' => $this->note,
            'order_no' => $order->order_no ?? ""
        ];
    }
}
