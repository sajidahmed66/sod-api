<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return SliderResource::collection(
            executeQuery(
                Slider::query()
                    ->where('vendor_id', Auth::user()->vendor_id)
                    ->orderBy('sort')
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return SliderResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|max:255',
            'sub_title' => 'nullable|max:255',
            'sort' => 'required|integer|min:1',
            'image' => 'required|image|max:1024'
        ]);

        $user = Auth::user();

        $data['vendor_id'] = $user->vendor_id;
        $data['image'] = resizeImage($user->vendor_id, $request->file('image'), 2376, 807, 'sliders');

        return new SliderResource(Slider::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider)
    {
        if ($slider->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return new SliderResource($slider);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $slider
     * @return bool
     */
    public function update(Request $request, Slider $slider)
    {
        if ($slider->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validate([
            'title' => 'nullable|max:255',
            'sub_title' => 'nullable|max:255',
            'sort' => 'required|integer|min:1',
            'image' => 'nullable|image|max:1024'
        ]);

        if ($request->hasFile('image')) {
            if ($slider->image && Storage::exists($slider->image)) {
                Storage::delete($slider->image);
            }

            $data['image'] = resizeImage(Auth::user()->vendor_id, $request->file('image'), 2376, 807, 'sliders');
        } else {
            unset($data['image']);
        }

        return $slider->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $slider
     * @return bool
     */
    public function destroy(Slider $slider)
    {
        if ($slider->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return $slider->delete();
    }
}
