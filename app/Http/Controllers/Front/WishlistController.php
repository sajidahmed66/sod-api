<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return WishlistResource::collection($user->wishlist->load('product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer'
        ]);

        $user = $request->user();

        $product = Product::where('id', $data['product_id'])
            ->where('vendor_id', $user->vendor_id)
            ->first();

        if ($product) {
            $wishlist = Wishlist::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            if ($wishlist) {
                $wishlist->delete();

                return response()->json(['data' => false]);
            } else {
                Wishlist::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id
                ]);

                return response()->json(['data' => true]);
            }
        } else {
            return response()->json(['message' => 'Not found.'], 404);
        }
    }
}
