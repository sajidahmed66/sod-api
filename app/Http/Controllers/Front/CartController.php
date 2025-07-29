<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\Types\Nullable;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $carts = Cart::where(function($q) use ($request, $user) {
                if ($user)
                    $q->where('user_id', $user->id);
                else
                    $q->where('app_id', $request->header('AppId'));
            })
            ->where('vendor_id', $request->header('Vendor'))
            ->with('product', 'productPrice')
            ->get();

        return CartResource::collection($carts);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'quantity' => 'required|integer',
            'product_id' => 'required|integer|exists:products,id',
            'product_price_id' => 'nullable|integer',
        ]);

        $user = $request->user();

        $cart = Cart::where(function($q) use ($request, $user, $data) {
                if ($user)
                    $q->where('user_id', $user->id);
                else
                    $q->where('app_id', $request->header('AppId'));

                if (isset($data['product_price_id']) && $data['product_price_id'])
                    $q->where('product_price_id', $data['product_price_id']);
            })
            ->where('vendor_id', $request->header('Vendor'))
            ->where('product_id', $data['product_id'])
            ->first();

        $productPrices = ProductPrice::where('product_id', $data['product_id'])->get();

        if (count($productPrices) == 1) {
            $data['product_price_id'] = $productPrices[0]->id;
        }

        if (!$cart) {
            $cart = new Cart();
            $cart->vendor_id = $request->header('Vendor');
            $cart->app_id = $user ? null : $request->header('AppId');
            $cart->user_id = $user ? $user->id : null;
            $cart->product_id = $data['product_id'];
            if (isset($data['product_price_id']) && $data['product_price_id']) {
                $cart->product_price_id = (integer)$data['product_price_id'];
            }
            $cart->quantity = 0;
            $cart->save();
        }

        $cart->increment('quantity', $data['quantity']);

        if ($cart->quantity < 1) {
            $cart->quantity = 1;
            $cart->save();
        }

        return $this->index($request);
    }

    public function destroy(Cart $cart, Request $request)
    {
        $user = $request->user();

        if (($cart->app_id == $request->header('AppId')) || ($user && $user->id == $cart->user_id)) {
            $cart->delete();

            return $this->index($request);
        }

        return response()->json(['message' => 'Unauthorized.'], 401);
    }

    public function sync($user, $request)
    {
        $carts = Cart::where('app_id', $request->header('AppId'))
            ->where('vendor_id', $request->header('Vendor'))
            ->get();

        foreach ($carts as $cart) {
            $exists = Cart::where('user_id', $user->id)
                ->where('product_id', $cart->product_id)
                ->first();

            if ($exists) {
                $exists->increment('quantity', $cart->quantity);
                $cart->delete();
            } else {
                $cart->update([
                    'app_id' => null,
                    'user_id' => $user->id
                ]);
            }
        }
    }
}
