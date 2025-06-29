<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Jobs\SendSms;
use App\Models\SmsLog;
use App\Models\User;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Date;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|numeric|digits:11',
            'email' => 'nullable|email',
            'password' => 'required',
            'device' => 'required|string'
        ]);

        if ($data['email']) {
            $user = User::where('email', $request->email)
                ->where('vendor_id', $request->header('Vendor'))
                ->first();

            if ($user) {
                throw ValidationException::withMessages([
                    'email' => ['This email already taken.'],
                ]);
            }
        }

        $user = User::where('mobile', $request->mobile)
            ->where('vendor_id', $request->header('Vendor'))
            ->first();

        if ($user) {
            throw ValidationException::withMessages([
                'mobile' => ['This mobile number already taken.'],
            ]);
        }

        $data['vendor_id'] = $request->header('Vendor');
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        $user->token = $user->createToken($request->device)->plainTextToken;

//        $cartController = new CartController();
//        $cartController = (new CartController)->sync();
        (new CartController)->sync($user, $request);

        return new AuthResource($user);
    }

    public function login(Request $request)
    {
        $messages = [
            'email.required' => 'This field is required.'
        ];

        $data = $request->validate([
            'email' => 'required',
            'password' => 'required',
            'device' => 'required|string'
        ], $messages);

        $user = User::where(function($q) use ($data) {
                $q->where('email', $data['email']);
                $q->orWhere('mobile', $data['email']);
            })
            ->where('vendor_id', $request->header('Vendor'))
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->token = $user->createToken($request->device)->plainTextToken;

//        $cartController = new CartController();
        (new CartController)->sync($user, $request);

        return new AuthResource($user);
    }

    public function user(Request $request)
    {
        return response()->json([
            'data' => $request->user()
        ]);
    }

    public function logout()
    {
        if (Auth::guard('customer')->user())
            Auth::guard('customer')->user()->currentAccessToken()->delete();
    }

    public function sendOtp(Request  $request)
    {
        $currentTime = Carbon::now()->addMinute(-30)->format('Y-m-d H:i:s');

        $flag = 'failed';
        try {
            $req = $request->all();
            $vendorId = $request->header('Vendor');
            $data['otp'] = mt_rand(100000, 999999);
            $data['send_otp_at'] = Carbon::now()->format('Y-m-d H:i:s');

            $user = User::where('vendor_id', $vendorId)->where('mobile', $req['mobile'])->first();

            if (!is_null($user) && $user->send_otp_at > $currentTime) {
                return 'otp_timeout';
            }
            else if (!is_null($user)) {

                $messages = "Your Otp: ". $data['otp'];

                $smsLog = SmsLog::create([
                    'vendor_id' => $request->header('Vendor'),
                    'to' => $user->mobile,
                    'order_id' => null,
                    'user_id' => $user ? $user->id : null,
                    'message' => $messages,
                    'status_text' => 'Pending-Forgot-Password'
                ]);
                SendSms::dispatch($smsLog);

                $user->update($data);
                $flag = "success";
            }
            else {
                return 'user_not_found';
            }
        } catch (\Exception $e){

            return 'failed';
        }

        return $flag;
    }

    public function verifyOtp(Request $request)
    {
        $flag = 'failed';
        $req = $request->all();
        $vendorId = $request->header('Vendor');
        $user = User::where('vendor_id', $vendorId)->where('mobile', $req['mobile'])->first();

        if ($user->otp == $req['otp']) {
            return 'success';
        }
        else return 'failed';
    }

    public function setNewPassword(Request $request)
    {
        $req = $request->all();
        $data['password'] = bcrypt($req['password']);
        $vendorId = $request->header('Vendor');
        $user = User::where('vendor_id', $vendorId)->where('mobile', $req['mobile'])->first();

        if ($user->update($data)) {
            return 'success';
        }
        else return 'failed';
    }
}
