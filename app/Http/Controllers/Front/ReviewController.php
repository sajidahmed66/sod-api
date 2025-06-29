<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function getReview($productId)
    {
        $reviews = Review::where('product_id', $productId)->latest()->get();

        return ReviewResource::collection($reviews);
    }

    public function addReview(Request $request)
    {
        $data = $request->validate([
            'comment' => 'required|max:255',
            'star' => 'required|integer|between:1,5',
            'product_id' => 'required'
        ]);

        $user = Auth::user();

        if ($this->checkEligibility($data['product_id'])) {
            Review::create([
                'user_id' => $user->id,
                'product_id' => $data['product_id'],
                'star' => $data['star'],
                'comment' => $data['comment'],
            ]);
        }
    }

    public function checkEligibility($productId)
    {
        $user = Auth::user();

        $ordered = OrderItem::where('product_id', $productId)
            ->whereHas('order', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->first();

        $previousReview = Review::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        return $ordered && !$previousReview;
    }
}
