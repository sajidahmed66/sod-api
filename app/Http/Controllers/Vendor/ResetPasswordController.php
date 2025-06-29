<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function resetPasswordEmail(Request $request)
    {
        $credentials = request()->validate(['email' => 'required|email']);

        Password::broker('vendors')->sendResetLink($credentials);

        return response()->json(["msg" => 'Reset password link sent on your email id.']);
    }

    public function checkToken($vendor, $token, $email)
    {
        $settings = Setting::where('vendor_id', $vendor)->first();

        return redirect('https://'.$settings->admin_host.'/reset?token='.$token.'&email='.$email);
    }

    public function reset() {
        $credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $reset_password_status = Password::broker('vendors')->reset($credentials, function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json([
                "errors" => [
                    "token" => ["Invalid token provided"]
                ]
            ], 422);
        }

        return response()->json(["msg" => "Password has been successfully changed"]);
    }
}
