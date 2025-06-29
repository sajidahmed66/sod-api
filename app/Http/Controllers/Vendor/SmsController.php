<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SmsLogResource;
use App\Models\SmsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsController extends Controller
{
    public function smsLogs()
    {
        return SmsLogResource::collection(
            executeQuery(
                SmsLog::query()->where('sms_logs.vendor_id', Auth::user()->vendor_id)->with('order')
            )
        );
    }
}
