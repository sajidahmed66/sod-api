<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $payment = $this->payment;

        if ($this->status != 'Shipped') {
            if ($this->due <= 0) {
                $payment = 'Payment Complete.';
            } elseif ($this->paid > 0) {
                $payment = 'Partially Paid.';
            }
        } else {
            if ($this->due <= 0) {
                $payment = 'Payment Complete.';
            }
        }

        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'name' => $this->name,
            'area' => new AreaResource($this->whenLoaded('area')),
            'city' => new CityResource($this->whenLoaded('city')),
            'mobile' => $this->mobile,
            'address' => $this->address,
            'status' => $this->status,
            'payment' => $payment,
            'total' => $this->total,
            'sub_total' => $this->sub_total,
            'shipping_cost' => $this->shipping_cost,
            'discount' => $this->discount,
            'paid' => $this->paid,
            'due' => $this->due,
            'note' => $this->note,
            'courier_branch' => $this->courier_branch,
            'courier_tracking_no' => $this->courier_tracking_no,
            'courier_status' => $this->courier_status,
            'courier' => new CourierResource($this->whenLoaded('courier')),
            'status_histories' => OrderStatusResource::collection($this->whenLoaded('statusHistories')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'created_at' => $this->created_at->format('M j, Y h:i A'),
            'shipping_date' => $this->shipping_date
        ];
    }
}
