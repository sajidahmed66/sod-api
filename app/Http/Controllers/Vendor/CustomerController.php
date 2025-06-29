<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return UserResource::collection(
            executeQuery(
                User::query()->where('vendor_id', $user->vendor_id)->with('addresses.city', 'addresses.area')
            )
        );
    }

    public function show(User $customer)
    {
        return new UserResource(
            $customer->load(['addresses.area', 'addresses.city', 'orders' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
        );
    }

    public function destroy(User $customer)
    {
        if ($customer->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return $customer->delete();
    }

    public function getCustomerData($mobile){
        $vendorId = Auth::user()->vendor_id;
        $customer = null;

        $order = Order::where('vendor_id', $vendorId)
            ->where('mobile', $mobile)
            ->latest()
            ->first();

        if (!$order) {
            $customer = User::where('vendor_id', $vendorId)
                ->where('mobile', $mobile)->first();
        }

        if (!$order && !$customer) {
            return response()->json([
                'data' => null
            ]);
        }

        $totalOrder = Order::where('vendor_id', $vendorId)
            ->where('mobile', $mobile)
            ->count();
        $cancelOrder = Order::where('vendor_id', $vendorId)
            ->where('mobile', $mobile)
            ->where('status', 'Cancelled')
            ->count();

        return [
            "data" => [
                "id" => $order->order_no,
                'name' => $order ? $order->name : $customer->name,
                'city_id' => $order ? $order->city_id : '',
                'area_id' => $order ? $order->area_id : '',
                'address' => $order ? $order->address : '',
                "total_orders" => $totalOrder,
                "cancel_orders" => $cancelOrder
            ]
        ];
    }
}
