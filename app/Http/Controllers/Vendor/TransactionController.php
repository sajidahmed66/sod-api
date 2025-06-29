<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TransactionResource;
use App\Jobs\SendSms;
use App\Models\Category;
use App\Models\NotificationSetting;
use App\Models\Order;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\Transaction;
use App\Services\AccountService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    private $accountingService;

    public function __construct(AccountService $accountingService)
    {
        $this->accountingService = $accountingService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return TransactionResource::collection(
            executeQuery(Transaction::query()->where('transactions.vendor_id', Auth::user()->vendor_id)->with('order'))
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return TransactionResource
     */
    public function store(Request $request)
    {
        $rules = [
            'payment_method' => 'required|in:bKash,Nagad,Rocket,Cash,Cheque',
            'amount' => 'required|numeric:min:1',
            'note' => 'nullable|max:255',
            'order_id' => 'required',
        ];

        if (!in_array($request->payment_method, ["Cash", "Cheque"])) {
            $rules['transaction_no'] = "required|string|max:255";
        }

        $data = $request->validate($rules);

        $order = Order::find($data['order_id']);

        if (!$order || $order->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        $data['vendor_id'] = Auth::user()->vendor_id;
        $data['status'] = 1;

        $transaction = Transaction::create($data);

        $order->paid += $transaction->amount;
        $order->due -= $transaction->amount;
        if ($order->due < 0) {
            $order->due = 0;
        }
        $order->status = 'Confirmed';
        $order->save();

        $accountingData = [
            'vendor_id' => $data['vendor_id'],
            'order_id' => $data['order_id'],
            'date' => Carbon::now(),
            'type' => "Income",
            'qty' => 0,
            'amount' => $data['amount']
        ];

        $this->accountingService->upsertFromInventory($accountingData);

        // Send Notification
        $notificationSettings = NotificationSetting::where('vendor_id', Auth::user()->vendor_id)
            ->first();

        $settings = Setting::where('vendor_id', Auth::user()->vendor_id)->first();

        if ($notificationSettings && $notificationSettings->customer_payment_approved_sms) {
            $messages = "Your ".$settings->host." Order #".$order->order_no." payment of ".$transaction->amount." BDT has been confirmed. Helpline: ".$settings->phone;

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

        return new TransactionResource($transaction);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return bool
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        if ($transaction->status !== 0)
            return response()->json(['message' => 'Already Updated.'], 402);

        $data = $request->validate([
            'status' => 'required|integer|min:0|max:2'
        ]);

        $transaction->update($data);

        $order = $transaction->order;
        $order->paid += $transaction->amount;
        $order->due -= $transaction->amount;

        /*if ($order->status === "Pending") {
            // Send Notification
            $notificationSettings = NotificationSetting::where('vendor_id', Auth::user()->vendor_id)
                ->first();

            $settings = Setting::where('vendor_id', Auth::user()->vendor_id)->first();

            if ($notificationSettings && $notificationSettings->customer_payment_approved_sms) {
                $messages = "Your ".$settings->host." Order #".$order->order_no." payment of ".$transaction->amount." BDT has been confirmed. Helpline: ".$settings->phone;

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

            $order->status = "Confirmed";
        }*/

        $order->save();

        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
