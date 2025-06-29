<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SettingResource extends JsonResource
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
            'vendor_id' => $this->vendor_id,
            'name' => $this->vendor ? $this->vendor->name : null,
            'logo' => $this->logo ? Storage::url($this->logo) : null,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'about' => $this->about,
            'facebook_page_id' => $this->facebook_page_id,
            'facebook_pixel_id' => $this->facebook_pixel_id,
            'steadfast_api_key' => $this->steadfast_api_key,
            'steadfast_api_secret' => $this->steadfast_api_secret,
            'bkash_no' => $this->bkash_no,
            'bkash_type' => $this->bkash_type,
            'nagad_no' => $this->nagad_no,
            'nagad_type' => $this->nagad_type,
            'rocket_no' => $this->rocket_no,
            'rocket_type' => $this->rocket_type,
            'shipping_cost_inside_dhaka' => $this->shipping_cost_inside_dhaka,
            'shipping_cost_outside_dhaka' => $this->shipping_cost_outside_dhaka,
            'shipping_note' => $this->shipping_note,
            'gtag_id' => $this->gtag_id,
            'pathao_client_id' => $this->pathao_client_id,
            'pathao_client_secret' => $this->pathao_client_secret,
            'pathao_store_id' => $this->pathao_store_id,
            'pathao_password' => $this->pathao_password,
        ];
    }
}
