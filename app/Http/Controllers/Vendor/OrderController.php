<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\OrderResource;
use App\Jobs\SendSms;
use App\Mail\OrderPlaced;
use App\Models\Address;
use App\Models\Courier;
use App\Models\NotificationSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\User;
use App\Services\InventoryLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;

class OrderController extends Controller
{

    private  $inventoryLogService;

    public function __construct(InventoryLogService $inventoryLogService)
    {
        $this->inventoryLogService = $inventoryLogService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->order_status && $request->order_status != "")
            $query->where('status', $request->order_status);

        return OrderResource::collection(
            executeQuery(
                $query->where('vendor_id', Auth::user()->vendor_id)->with('courier')
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return OrderResource
     */
    public function store(OrderStoreRequest $request)
    {
        $data = $request->validated();

        $user = Auth::user();
        $items = $data['items'];

        $customer = null;

        if ($data['user_id']) {
            $customer = User::find($data['user_id']);
        }

        $data['vendor_id'] = $user->vendor_id;
        $data['order_no'] = random_int(10000000, 99999999);
        $data['sub_total'] = 0;
        $data['created_by'] = $user->id;

        unset($data['items']);

        $order = Order::create($data);

        $orderItemCollection = $this->getOrderItemCollection($items, $order, $user);
        $itemsCollection = $orderItemCollection['items'];
        $subTotal = $orderItemCollection['sub_total'];

        $order->items()->saveMany($itemsCollection);

        $order->sub_total = $subTotal;
        $order->total = $subTotal + $data['shipping_cost'] - $data['discount'];
        $order->discount = $data['discount'];
        $order->due = $subTotal + $data['shipping_cost'] - $data['discount'];
        $order->shipping_date = $data['shipping_date'];
        $order->save();

        $notificationSettings = NotificationSetting::where('vendor_id', $user->vendor_id)
            ->first();
        $settings = Setting::where('vendor_id', $user->vendor_id)->first();

        if ($customer && $customer->email && $notificationSettings && $notificationSettings->customer_new_order_email == 1)
            Mail::to($user->email)->send(new OrderPlaced($order));

        if ($notificationSettings && $notificationSettings->customer_new_order_sms) {
            $messages = "Your ".$settings->host." Order #".$order->order_no." has been placed. Helpline: ".$settings->phone;

            $smsLog = SmsLog::create([
                'vendor_id' => $user->vendor_id,
                'to' => $order->mobile,
                'order_id' => $order->id,
                'user_id' => $customer ? $customer->id : null,
                'message' => $messages,
                'status_text' => 'PENDING'
            ]);

            SendSms::dispatch($smsLog);
        }

        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return OrderResource
     */
    public function show(Order $order)
    {
        if ($order->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized.'], 401);

        return new OrderResource($order->load('area', 'city', 'courier',
            'items.product.category', 'transactions', 'statusHistories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return bool
     */
    public function update(OrderStoreRequest $request, Order $order)
    {
        if ($order->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validated();

        $items = $data['items'];

        unset($data['items']);
        unset($data['user_id']);
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $order->update($data);
            $order->items()->delete();
            $this->inventoryLogService->deleteInventoryLogByOrderId($order->id, $user->vendor_id);

            $orderItemCollection = $this->getOrderItemCollection($items, $order, $user);
            $itemsCollection = $orderItemCollection['items'];
            $subTotal = $orderItemCollection['sub_total'];

            $order->items()->saveMany($itemsCollection);

            $order->sub_total = $subTotal;
            $order->total = $subTotal + $data['shipping_cost'] - $data['discount'];
            $order->discount = $data['discount'];
            $order->due = $subTotal + $data['shipping_cost'] - $data['discount'];

            $order->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::alert($e->getMessage());
        }
    }

    public function getOrderItemCollection($items, $order, $user)
    {
        $itemsCollection = collect([]);
        $subTotal = 0;

        foreach ($items as $item) {
            $product = Product::where('vendor_id', $order->vendor_id)
                ->where('id', $item['product_id'])
                ->first();

            if ($product) {
                $productPrice = null;
                $this->inventoryLogService->saveLog($product->id, $user->vendor_id, $item['quantity'], 'Sold', $order->id);

                if ($item['product_price_id']) {
                    $productPrice = ProductPrice::where('id', $item['product_price_id'])->first();
                }

                $itemsCollection->push(new OrderItem([
                    'vendor_id' => $order->vendor_id,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price_id' => $productPrice ? $productPrice->id : null,
                    'product_price_name' => $productPrice ? $productPrice->name : null,
                    'product_sub_text' => $product->sub_text,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'original_unit_price' => $item['original_unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]));

                $subTotal += $item['quantity'] * $item['unit_price'];
            }
        }

        return [
            'sub_total' => $subTotal,
            'items' => $itemsCollection
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if ($order->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        $this->inventoryLogService->deleteInventoryLogByOrderId($order->id, Auth::user()->vendor_id);

        return $order->delete();
    }

    public function changeStatus(Order $order, Request $request)
    {
        $rules = [
            'status' => 'required|in:Pending,Confirmed,Shipped,Completed,Cancelled',
        ];

        if ($request->status === 'Shipped') {
            $rules['courier'] = 'nullable|exists:couriers,id';
            $rules['tracking_no'] = 'nullable|max:255';
            $rules['branch'] = 'nullable|max:255';
        }

        if ($request->status === 'Confirmed') {
            $rules['delivery_platform'] = 'nullable|string';
            $rules['cod_amount'] = 'nullable|numeric|min:0';
            if ($request->delivery_platform) {
                $rules['weight'] = 'numeric|min:0';
            }
            $rules['note'] = 'nullable|string|max:255';
        }

        $data = $request->validate($rules);

        $prevStatus = $order->status;

        if ($order->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized.'], 401);

        if ($order->status != $data['status']) {
            $order->status = $data['status'];
            $order->last_status_update_at = Carbon::now();

            $notificationSettings = NotificationSetting::where('vendor_id', Auth::user()->vendor_id)
                ->first();

            $settings = Setting::where('vendor_id', Auth::user()->vendor_id)->first();

            if ($notificationSettings && $notificationSettings->customer_order_status_change_sms) {
                if ($data['status'] === "Shipped") {
                    $courierInfo = "";

                    if ($data['courier']) {
                        $courier = Courier::find($data['courier']);

                        if ($courier) {
                            $courierInfo = $courier->name;

                            if ($data['branch'])
                                $courierInfo .= ", " . $data['branch'];

                            if ($data['tracking_no'])
                                $courierInfo .= ". Tracking #" . $data['tracking_no'];

                            $courierInfo .= ". ";
                        }
                    }

                    $messages = "Your " . $settings->host . " Order #" . $order->order_no . " has been shipped. ".$courierInfo."Helpline: " . $settings->phone;
                } else {
                    $messages = "Your ".$settings->host." Order #".$order->order_no." status has been changed to ".$data['status'].". Helpline: ".$settings->phone;
                }

                $smsLog = SmsLog::create([
                    'vendor_id' => Auth::user()->vendor_id,
                    'to' => $order->mobile,
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'message' => $messages,
                    'status_text' => 'PENDING'
                ]);

                SendSms::dispatch($smsLog);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $data['status'],
                'changed_by' => Auth::user()->id
            ]);
        }

        if ($data['status'] === "Shipped") {
            $order->courier_id = $data['courier'];
            $order->courier_branch = $data['branch'];
            $order->courier_tracking_no = $data['tracking_no'];
        }

        $order->save();

        if ($prevStatus !== 'Confirmed' && $data['status'] === "Confirmed") {

            if ($data['delivery_platform'] == "steadfast") {

                if ($settings->steadfast_api_key && $settings->steadfast_api_secret) {
                    $res = Http::withHeaders([
                        'Api-Key' => $settings->steadfast_api_key,
                        'Secret-Key' => $settings->steadfast_api_secret,
                    ])->post('https://portal.steadfast.com.bd/api/v1/create_order', [
                        'invoice' => $order->order_no,
                        'recipient_name' => $order->name,
                        'recipient_phone' => $order->mobile,
                        'recipient_address' => $order->address . ', ' . $order->area->name_bn . ', ' . $order->city->name_bn,
                        'cod_amount' => $data['cod_amount'],
                        'note' => $data['note'],
                    ]);

                    if (!$res->successful() || $res->json()['status'] !== 200) {
                        return response()->json(['message' => 'Sorry! Cannot create SteadFast order.'], 201);
                    } elseif ($res->successful() && $res->json()['status'] === 200) {
                        $order->courier_id = 13;
                        $order->courier_tracking_no = $res->json()['consignment']['tracking_code'];
                        $order->save();
                    }
                } else {
                    return response()->json(['message' => 'SteadFast api key not found'], 201);
                }
            }
            elseif($data['delivery_platform'] == "pathao") {
//                dd("here");
                $res = Http::withHeaders([

                ])->post('https://api-hermes.pathao.com/aladdin/api/v1/issue-token', [
                    "client_id"=> $settings->pathao_client_id,
                    "client_secret"=> $settings->pathao_client_secret,
                    "username"=> "sahab.221b@gmail.com",
//                    "username"=> Auth::user()->email,
                    "password"=> $settings->pathao_password,
                    "grant_type"=> "password"
                ]);
                if (!$res->successful()) {
                    return response()->json(['message' => 'Sorry! Cannot create Pathao order1.'], 201);
                }

                $response = $res->json();
                $accessToken = $response['access_token'];
                $data = [
                    "store_id" => $settings->pathao_store_id,
                    "recipient_name"=> $order->name,
                    "recipient_phone"=> $order->mobile,
                    "recipient_city"=> 1,
                    "recipient_zone"=> 1,
                    "recipient_address" => $order->address,
                    "delivery_type"=> 48,
                    "amount_to_collect"=> $order->due,
                    "item_quantity" => count($order->items),
                    "item_weight" => (int)$data['weight'],
                    "item_type" => 2
                ];

                $res = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken
                ])->post('https://api-hermes.pathao.com/aladdin/api/v1/orders', $data);
                if (!$res->successful()) {
                    return response()->json(['message' => 'Sorry! Cannot create Pathao order2.'], 201);
                }
            }
        }

        return response()->json(['message' => 'Success']);
    }

    public function getInvoice(Order $order)
    {
        if ($order->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized.'], 401);


        $order->load('vendor.settings', 'area', 'city', 'items');

        $response = [];
        foreach ($order->items as $item) {
            $item['original_unit_price'] = $item->original_unit_price ?? $item->unit_price;
            $response[] = $item;
        }

        $order->items = $response;

        $data = [
            'order' => $order,
            'shippingDate' => null
        ];


        if ($order->status == 'Shipped') {
            $OrderHistory = OrderStatusHistory::where('order_id', $order->id)->first();
            $data['shippingDate'] = ($OrderHistory->created_at)->format('M j, Y h:i A') ?? null;

        }
//        dd($data['shippingDate']);
        $pdf = LaravelMpdf::loadView('pdf.invoice', $data);

        return $pdf->output();
    }
}
