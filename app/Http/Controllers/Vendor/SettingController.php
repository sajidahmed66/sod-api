<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourierResource;
use App\Http\Resources\NotificationSettingResource;
use App\Http\Resources\SettingResource;
use App\Models\Courier;
use App\Models\NotificationSetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'logo' => 'nullable|image|max:1024',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:150',
            'about' => 'nullable|string|max:255',
            'facebook_page_id' => 'nullable|string|max:255',
            'facebook_pixel_id' => 'nullable|string|max:255',
            'steadfast_api_key' => 'nullable|max:255',
            'steadfast_api_secret' => 'nullable|max:255',
            'pathao_client_id' => 'nullable|max:255',
            'pathao_client_secret' => 'nullable|max:255',
            'pathao_store_id' => 'nullable|max:255',
            'pathao_password' => 'nullable|max:255',
        ]);

        $user = Auth::user();
        $setting = Setting::where('vendor_id', $user->vendor_id)->first();

        if ($setting) {
            if ($request->hasFile('logo')) {
                if ($setting->logo && Storage::exists($setting->logo)) {
                    Storage::delete($setting->logo);
                }

                $data['logo'] = resizeImage($user->vendor_id, $request->file('logo'), 180, null, 'logo');
            } else {
                unset($data['logo']);
            }

            $setting->update($data);

            return new SettingResource($setting);
        } else {
            return response()->json(['Unauthorized.'], 401);
        }
    }

    public function paymentMethodsStore(Request $request)
    {
        $data = $request->validate([
            'bkash_no' => 'nullable|numeric|digits:11',
            'bkash_type' => 'required|in:Personal,Merchant',
            'nagad_no' => 'nullable|numeric|digits:11',
            'nagad_type' => 'required|in:Personal,Merchant',
            'rocket_no' => 'nullable|numeric|digits_between:11,12',
            'rocket_type' => 'required|in:Personal,Merchant',
        ]);

        $user = Auth::user();
        $setting = Setting::where('vendor_id', $user->vendor_id)->first();

        if ($setting) {
            $setting->update($data);

            return new SettingResource($setting);
        } else {
            return response()->json(['Unauthorized.'], 401);
        }
    }

    public function shippingCostStore(Request $request)
    {
        $data = $request->validate([
            'shipping_cost_inside_dhaka' => 'required|numeric|min:0',
            'shipping_cost_outside_dhaka' => 'required|numeric|min:0',
            'shipping_note' => 'nullable|max:255',
        ]);

        $user = Auth::user();
        $setting = Setting::where('vendor_id', $user->vendor_id)->first();

        if ($setting) {
            $setting->update($data);

            return new SettingResource($setting);
        } else {
            return response()->json(['Unauthorized.'], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return SettingResource
     */
    public function show()
    {
        $user = Auth::user();
        $setting = Setting::where('vendor_id', $user->vendor_id)->first();

        if ($setting)
            return new SettingResource($setting);
        else
            return response()->json(['Not found.'], 404);
    }

    public function getCouriers()
    {
        return CourierResource::collection(Courier::orderBy('name')->get());
    }

    public function getNotificationsSettings(Request $request)
    {
        $user = Auth::user();
        $notificationSettings = NotificationSetting::where('vendor_id', $user->vendor_id)->first();

        if (!$notificationSettings)
            return response()->json(['Not found.'], 404);

        return new NotificationSettingResource($notificationSettings);
    }

    public function setNotificationsSettings(Request $request)
    {
        $data = $request->validate([
            'customer_new_order_email' => 'required|boolean',
            'customer_order_status_change_email' => 'required|boolean',
            'customer_payment_approved_email' => 'required|boolean',
            'customer_new_order_sms' => 'required|boolean',
            'customer_order_status_change_sms' => 'required|boolean',
            'customer_payment_approved_sms' => 'required|boolean',
        ]);

        $user = Auth::user();
        $notificationSettings = NotificationSetting::where('vendor_id', $user->vendor_id)->first();

        if (!$notificationSettings) {
            $notificationSettings = NotificationSetting::create([
                'vendor_id' => $user->vendor_id
            ]);
        }

        return $notificationSettings->update($data);
    }
}
