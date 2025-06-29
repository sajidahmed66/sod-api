<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsLogResource extends JsonResource
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
            'to' => $this->to,
            'order' => new OrderResource($this->whenLoaded('order')),
            'message' => $this->message,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'created_at' => $this->created_at->format('M j, Y h:i A')
        ];
    }
}
