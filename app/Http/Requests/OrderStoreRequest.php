<?php

namespace App\Http\Requests;

use App\Rules\ProductVarietyRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required_if' => 'The name field is required.',
            'mobile.required_if' => 'The mobile field is required.',
            'area_id.required_if' => 'The area field is required.',
            'city_id.required_if' => 'The city field is required.',
            'payment.required_if' => 'The payment field is required.',
            'address.required_if' => 'The address field is required.',
            'items.*.quantity.required' => 'The quantity field is required.',
            'items.*.quantity.integer' => 'The quantity must be an integer.',
            'items.*.quantity.min' => 'The quantity must be at least 1.',
            'items.*.unit_price.required' => 'The unit price field is required.',
            'items.*.unit_price.numeric' => 'The unit price must be an numeric.',
            'items.*.unit_price.min' => 'The unit price must be at least 0.',
            'items.*.product_id.required' => 'The product field is required.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'nullable',
            'shipping_date' => 'nullable',
            'name' => 'required|max:150',
            'mobile' => 'required|size:11',
            'area_id' => 'nullable',
            'city_id' => 'required',
            'address' => 'required|min:10|max:255',
            'payment' => 'required|in:Cash On Delivery',
            'note' => 'nullable|max:255',
            'items.*.product_id' => 'required|integer',
            'items.*.product_price_id' => [new ProductVarietyRule()],
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.original_unit_price' => 'nullable|numeric|min:0',
            'shipping_cost' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
        ];
    }
}
