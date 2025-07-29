<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicOrderRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\AreaResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\OrderResource;
use App\Jobs\SendSms;
use App\Mail\OrderPlaced;
use App\Models\Address;
use App\Models\Area;
use App\Models\Cart;
use App\Models\City;
use App\Models\NotificationSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\Transaction;
use App\Models\Vendor;
use App\Services\AccountService;
use App\Services\InventoryLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{

    private  $inventoryLogService;
    private $accountingService;
    public function __construct(InventoryLogService $inventoryLogService, AccountService $accountingService)
    {
        $this->inventoryLogService = $inventoryLogService;
        $this->accountingService = $accountingService;
    }
    public function getOrderByOrderNo($orderNo)
    {
        $order = Order::where('order_no', $orderNo)->first();

        if (!$order)
            return response()->json(['message' => 'Not found.'], 404);

        return new OrderResource($order);
    }

    public function store(Request $request)
    {
        $messages = [
            'city_id.required' => 'The city field is required.',
            'area_id.required' => 'The area field is required.',
        ];

        $rules = [
            'payment' => 'required|in:Cash On Delivery'
        ];

        if (!$request->address_id || $request->address_id == "") {
            $rules['city_id'] = 'required';
            $rules['area_id'] = 'nullable';
            $rules['name'] = 'required|string|max:255';
            $rules['mobile'] = 'required|numeric|digits:11';
            $rules['address'] = 'required|string|max:500';
        } else {
            $rules['address_id'] = 'required';
        }

        $data = $request->validate($rules, $messages);

        $user = $request->user();

        $settings = Setting::where('vendor_id', $request->header('Vendor'))->first();

        $data['vendor_id'] = $request->header('Vendor');
        $data['user_id'] = $user ? $user->id : null;
        $data['sub_total'] = 0;

        if (!$request->address_id || $request->address_id == "") {
            if ($user) {
//                $addressController = new AddressController();
                $address = (new AddressController)->store($request);

                $data['address_id'] = $address['id'];
            }
        } else {
            $address = Address::find($request->address_id);
            $data['city_id'] = $address->city_id;
            $data['area_id'] = $address->area_id;
            $data['name'] = $address->name;
            $data['mobile'] = $address->mobile;
            $data['address'] = $address->address;

            $user->addresses()->where('id', '!=', $address->id)->update(['default' => 0]);
            $address->default = 1;
            $address->save();
        }

        if ($data['city_id'] == 2) {
            $data['shipping_cost'] = $settings->shipping_cost_inside_dhaka;
        } else {
            $data['shipping_cost'] = $settings->shipping_cost_outside_dhaka;
        }

        $data['order_no'] = random_int(10000000, 99999999);
        $data['last_status_update_at'] = Carbon::now();
        $order = Order::create($data);

        $items = collect([]);

        $carts = Cart::where(function($q) use ($request, $user) {
            if ($user)
                $q->where('user_id', $user->id);
            else
                $q->where('app_id', $request->header('AppId'));
        })->where('vendor_id', $request->header('Vendor'))
            ->with('product')
            ->get();

        $subTotal = 0;

        foreach ($carts as $cart) {
            $productPrice = null;

            if ($cart->product_price_id) {
                $productPrice = ProductPrice::where('id', $cart->product_price_id)
                    ->where('product_id', $cart->product->id)
                    ->first();
            }

            $unitPrice = $productPrice ? $productPrice->price : $cart->product->price;
            $this->inventoryLogService->saveLog($cart->product->id, $request->header('Vendor'), $cart->quantity, 'Sold', $order->id);
            $amount = $cart->quantity * $unitPrice;

            $items->push(new OrderItem([
                'vendor_id' => $request->header('Vendor'),
                'order_id' => $order->id,
                'product_id' => $cart->product->id,
                'product_price_id' => $productPrice ? $productPrice->id : null,
                'product_price_name' => $productPrice ? $productPrice->name : null,
                'product_name' => $cart->product->name,
                'product_sub_text' => $cart->product->sub_text,
                'quantity' => $cart->quantity,
                'unit_price' => $unitPrice,
                'original_unit_price' => $productPrice ? $productPrice->price : $cart->product->price,
                'total' => $amount,
            ]));

            $subTotal += $amount;

            $cart->delete();
        }

        $order->items()->saveMany($items);

        $total = $order->shipping_cost + $subTotal;

        $order->sub_total = $subTotal;
        $order->total = $total;
        $order->discount = 0;
        $order->due = $total;
        $order->save();

        $notificationSettings = NotificationSetting::where('vendor_id', $request->header('Vendor'))
            ->first();

        if ($user && $user->email && $notificationSettings && $notificationSettings->customer_new_order_email == 1)
            Mail::to($user->email)->send(new OrderPlaced($order));

        if ($notificationSettings && $notificationSettings->customer_new_order_sms) {
            $messages = "Your ".$settings->host." Order #".$order->order_no." has been placed. Helpline: ".$settings->phone;

            $smsLog = SmsLog::create([
                'vendor_id' => $request->header('Vendor'),
                'to' => $order->mobile,
                'order_id' => $order->id,
                'user_id' => $user ? $user->id : null,
                'message' => $messages,
                'status_text' => 'PENDING'
            ]);

            SendSms::dispatch($smsLog);
        }

        return $order->order_no;
    }

    public function storeV2(PublicOrderRequest $request)
    {
        $data = $request->all();
        $order = [];
        try {
            $vendor = Vendor::where('id', $data['vendor_id'])->first();
            if (!$vendor) {
                return response()->json(['message' => 'Vendor not found'], 404);

            }

            $product = Product::where('id', $data['product_id'])->first();
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);

            }

            $city = City::select('id')->where('name_en', ucfirst($data['city']))->first();
            if (!$city) {
                return response()->json(['message' => 'City not found'], 404);
            }

            $settings = Setting::where('vendor_id', $data['vendor_id'])->first();

            $data['sub_total'] = $product->price * $data['quantity'];

            $data['city_id'] = $city->id;

            if ($data['city_id'] == 2) {
                $data['shipping_cost'] = $settings->shipping_cost_inside_dhaka;
            } else {
                $data['shipping_cost'] = $settings->shipping_cost_outside_dhaka;
            }

            $data['order_no'] = random_int(10000000, 99999999);
            $data['last_status_update_at'] = Carbon::now();
            $data['total'] = $data['shipping_cost'] + $data['sub_total'];
            $data['paid'] = 0;
            $data['due'] = $data['total'];
            $data['status'] = "Pending";
            $data['mobile'] = $data['mobile_no'];
            $data['payment'] = "Cash On Delivery";

            $orderItem = [
                'vendor_id' => $data['vendor_id'],

                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sub_text' => $product->sub_text,
                'quantity' => $data['quantity'],
                'unit_price' => $product->price,
                'original_unit_price' => $product->original_price,
                'total' => $data['total'],
            ];
            unset($data['mobile_no']);
            unset($data['product_id']);
            unset($data['quantity']);
            unset($data['city']);

            $order = Order::create($data);
            $orderItem['order_id'] = $order->id;
            OrderItem::create($orderItem);

        } catch (\Exception $e) {
//            dd($e->getMessage());
            return response()->json(['message' => 'Order Create Fail.'], 404);
        }


        $notificationSettings = NotificationSetting::where('vendor_id', $request->header('Vendor'))
            ->first();

        if ($notificationSettings && $notificationSettings->customer_new_order_sms) {
            $messages = "Your ".$settings->host." Order #".$order->order_no." has been placed. Helpline: ".$settings->phone;

            $smsLog = SmsLog::create([
                'vendor_id' => $request->header('Vendor'),
                'to' => $order->mobile,
                'order_id' => $order->id,
                'message' => $messages,
                'status_text' => 'PENDING'
            ]);

            SendSms::dispatch($smsLog);
        }

        return $order->order_no;
    }

    public function pay(Order $order, Request $request)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:bKash,Nagad,Rocket',
            'amount' => 'required|numeric:min:1',
            'transaction_no' => 'required|string|max:255',
            'note' => 'nullable|max:255',
        ]);

        $data['order_id'] = $order->id;
        $data['vendor_id'] = $order->vendor_id;

        Transaction::create($data);

        $accountingData = [
            'vendor_id' => $data['vendor_id'],
            'order_id' => $data['order_id'],
            'date' => Carbon::now(),
            'type' => "Income",
            'qty' => 0,
            'amount' => $data['amount']
        ];

        $this->accountingService->upsertFromInventory($accountingData);

        return true;
    }

    public function getCities(Request $request)
    {
        $query = City::query();

        if (isset($request->q) && $request->q !== '') {
            $query->where('name_en', 'like', '%'.$request->q.'%');
            $query->orWhere('name_bn', 'like', '%'.$request->q.'%');
        }

        return CityResource::collection($query->orderBy('sort')->get());
    }

    public function getAreas(Request $request)
    {
        if (!isset($request->city_id) || $request->city_id == '') {
            return response()->json([
                'data' => []
            ]);
        }

        $query = Area::query();

        if (isset($request->q) && $request->q !== '') {
            $query->where(function($q) use ($request) {
                $q->where('name_en', 'like', '%'.$request->q.'%');
                $q->orWhere('name_bn', 'like', '%'.$request->q.'%');
            });
        }

        return AreaResource::collection(
            $query->where('city_id', $request->city_id)->get()
        );
    }
}
