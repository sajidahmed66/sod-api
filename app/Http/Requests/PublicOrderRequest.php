<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'vendor_id' => ['required'],
            'name' => ['required'],
            'address' => ['required'],
            'mobile_no' => ['required'],
            'city' => ['required'],
            'product_id' => ['required', 'numeric'],
            'quantity'=> ['required', 'numeric'],
        ];
    }

}
