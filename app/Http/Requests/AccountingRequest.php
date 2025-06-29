<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountingRequest extends FormRequest
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
            'date'              => ['required'],
            'type'              => ['required'],
            'qty'               => ['required', 'numeric'],
            'amount'            => ['required', 'numeric'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Date Required.',
            'qty.required' => 'Quantity Required.',
            'type.required' => 'Type Required.',
            'amount.required' => 'Amount Required.',
        ];
    }
}
