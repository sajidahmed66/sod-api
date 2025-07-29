<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user) {
            return AddressResource::collection($user->addresses->load('area', 'city'));
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function store(Request $request)
    {
        $messages = [
            'city_id.required' => 'The city field is required.',
            'area_id.required' => 'The area field is required.',
        ];

        $data = $request->validate([
            'city_id' => 'required',
            'area_id' => 'required',
            'name' => 'required|string|max:255',
            'mobile' => 'required|numeric|digits:11',
            'address' => 'required|max:500',
        ], $messages);

        $user = $request->user();

        $data['user_id'] = $user->id;

        $user->addresses()->update(['default' => 0]);

        return new AddressResource(Address::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        //
    }

    public function destroy(Address $address, Request $request)
    {
        $user = $request->user();

        if ($address->user_id != $user->id)
            return response()->json(['message' => 'Unauthorized.'], 401);

        if ($address->default && count($user->addresses) > 1) {
            $newDefault = $user->addresses()->where('id', '!=', $address->id)->first();
            $newDefault->update([
                'default' => 1
            ]);
        }

        return $address->delete();
    }
}
