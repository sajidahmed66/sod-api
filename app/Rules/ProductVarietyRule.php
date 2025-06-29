<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Contracts\Validation\Rule;

class ProductVarietyRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $index = explode('.', $attribute)[1];
        $productId = request()->input("items.{$index}.product_id");

        if (!$productId) {
            return true;
        } else {
            $productPricesCount = ProductPrice::where('product_id', $productId)->count();

            if ($productPricesCount && !$value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The variety field is required.';
    }
}
