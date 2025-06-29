<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\TopNotificationResource;
use App\Models\TopNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return TopNotificationResource::collection(
            executeQuery(
                TopNotification::query()->where('vendor_id', Auth::user()->vendor_id)
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return TopNotificationResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'notification' => 'required|string|max:255',
            'active' => 'required|boolean'
        ]);

        $data['vendor_id'] = Auth::user()->vendor_id;

        return new TopNotificationResource(TopNotification::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TopNotification  $topNotification
     * @return \Illuminate\Http\Response
     */
    public function show(TopNotification $topNotification)
    {
        if ($topNotification->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return new TopNotificationResource($topNotification);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TopNotification  $topNotification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TopNotification $topNotification)
    {
        if ($topNotification->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validate([
            'notification' => 'required|string|max:255',
            'active' => 'required|boolean'
        ]);

        return $topNotification->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TopNotification  $topNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy(TopNotification $topNotification)
    {
        if ($topNotification->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return $topNotification->delete();
    }
}
