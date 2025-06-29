<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'sub_category_id' => 'nullable|integer|exists:sub_categories,id',
            'sub_text' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1500',
            'image' => 'nullable|file|image|max:1024',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|gt:price',
            'available_qty' => 'nullable|numeric',
            'active' => 'nullable|in:true,false',
            'stock_out' => 'nullable|in:true,false',
            'upcoming_product' => 'nullable|in:true,false',
            'variety' => 'nullable|array',
            'variety.*.name' => 'required|max:255',
            'variety.*.price' => 'required|numeric|min:0',
            'variety.*.original_price' => 'nullable|numeric|min:0|gt:variety.*.price',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->variety) {
            $variety = [];

            foreach ($this->variety as $v)
                $variety[] = json_decode($v, true);

            $this->merge([
                'variety' => $variety,
            ]);
        }
    }
}
