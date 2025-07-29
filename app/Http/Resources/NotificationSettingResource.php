<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationSettingResource extends JsonResource
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
            'customer_new_order_email' =>  (bool) $this->customer_new_order_email,
            'customer_order_status_change_email' =>  (bool) $this->customer_order_status_change_email,
            'customer_payment_approved_email' =>  (bool) $this->customer_payment_approved_email,
            'customer_new_order_sms' =>  (bool) $this->customer_new_order_sms,
            'customer_order_status_change_sms' =>  (bool) $this->customer_order_status_change_sms,
            'customer_payment_approved_sms' =>  (bool) $this->customer_payment_approved_sms,
        ];
    }
}
