<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\SocialLinkResource;
use App\Http\Resources\StaticPageResource;
use App\Http\Resources\TopNotificationResource;
use App\Mail\OrderPlaced;
use App\Models\ContactEmail;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\SocialLink;
use App\Models\StaticPage;
use App\Models\TopNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class GeneralController extends Controller
{
    public function settings(Request $request)
    {
        $setting = Setting::where('host', str_replace('www.', '', $request->host))
            ->first();

        if ($setting)
            return new SettingResource($setting);
        else
            return response()->json(['Not found.'], 404);
    }

    public function topNotifications(Request $request)
    {
        $notifications = TopNotification::where('vendor_id', $request->header('Vendor'))
            ->where('active', 1)
            ->get();

        return TopNotificationResource::collection($notifications);
    }

    public function sliders(Request $request)
    {
        $sliders = Slider::where('vendor_id', $request->header('Vendor'))
            ->orderBy('sort')
            ->get();

        return SliderResource::collection($sliders);
    }

    public function contactUs(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1200',
        ]);

        $data['vendor_id'] = $request->header('Vendor');

        ContactEmail::create($data);

        return true;
    }

    public function staticPage($id, Request $request)
    {
        $page = StaticPage::where('vendor_id', $request->header('Vendor'))
            ->where('page_id', $id)
            ->first();

        if ($page)
            return new StaticPageResource($page);

        return response()->json([
            'data' => [
                'content' => ''
            ]
        ]);
    }

    public function socialLinks(Request $request)
    {
        $links = SocialLink::where('vendor_id', $request->header('Vendor'))
            ->get();

        return SocialLinkResource::collection($links);
    }

    public function mailTest()
    {
        $order = Order::find(14);

        Mail::to('shantotrs@gmail.com')->send(new OrderPlaced($order));
    }
}
