<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function getOrders(Request $request)
    {
        $user = $request->user();

        return OrderResource::collection($user->orders->sortByDesc('created_at'));
    }

    public function getOrderDetails(Order $order, Request $request)
    {
        $user = $request->user();

        if ($order->user_id != $user->id)
            return response()->json(['message' => 'Unauthorized.'], 401);

        return new OrderResource($order->load('area', 'city', 'courier',
            'items.product.category', 'transactions', 'statusHistories'));
    }

    public function changeAccountDetails(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|numeric|digits:11',
            'email' => 'nullable|email'
        ]);

        $user = $request->user();

        if ($data['email']) {
            $exists = User::where('email', $request->email)
                ->where('id', '!=', $user->id)
                ->where('vendor_id', $request->header('Vendor'))
                ->first();

            if ($exists) {
                throw ValidationException::withMessages([
                    'email' => ['This email already taken.'],
                ]);
            }
        }

        $exists = User::where('mobile', $request->mobile)
            ->where('id', '!=', $user->id)
            ->where('vendor_id', $request->header('Vendor'))
            ->first();

        if ($exists) {
            throw ValidationException::withMessages([
                'mobile' => ['This mobile number already taken.'],
            ]);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->mobile = $data['mobile'];
        $user->save();

        return new AuthResource($user);
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        $user = $request->user();

        if (!Hash::check($data['old_password'], $user->password)) {
            throw ValidationException::withMessages([
                'old_password' => ['The provided old password is incorrect.'],
            ]);
        }

        $user->password = bcrypt($data['new_password']);
        $user->save();

        return true;
    }
}
