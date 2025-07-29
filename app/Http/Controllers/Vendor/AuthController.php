<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Models\VendorUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device' => 'required|string'
        ]);

        $user = VendorUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->token = $user->createToken($request->device)->plainTextToken;

        return new AuthResource($user);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'old_password' => 'required|current_password:vendor',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = Auth::user();
        $user->password = bcrypt($data['password']);
        $user->save();

        $user->tokens()->delete();

        $user->token = $user->createToken($request->device)->plainTextToken;

        return new AuthResource($user);
    }

    public function vendorDetails(Request $request)
    {
        $settings = Setting::where('admin_host', str_replace('www.', '', $request->host))
            ->with('vendor')->first();

        if (!$settings)
            return response()->json(['message' => 'Not found.'], 404);

        return response()->json([
            'data' => [
                'name' => $settings->vendor->name,
                'logo' => $settings->logo ? Storage::url($settings->logo) : null,
            ]
        ]);
    }
}
