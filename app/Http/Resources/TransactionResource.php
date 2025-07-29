<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'order' => new OrderResource($this->whenLoaded('order')),
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'transaction_no' => $this->transaction_no,
            'note' => $this->note,
            'status' => $this->status,
            'created_at' => $this->created_at->format('M j, Y h:i A'),
        ];
    }
}
