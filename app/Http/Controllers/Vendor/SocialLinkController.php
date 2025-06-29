<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialLinkResource;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SocialLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return SocialLinkResource::collection(
            executeQuery(
                SocialLink::query()->where('vendor_id', Auth::user()->vendor_id)
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return SocialLinkResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'site' => 'required|string|in:Facebook,Youtube,Twitter,Instagram',
            'link' => 'required|max:255|url'
        ]);

        $exits = SocialLink::where('vendor_id', Auth::user()->vendor_id)
            ->where('site', $data['site'])
            ->first();

        if ($exits) {
            throw ValidationException::withMessages([
                'site' => ['The site already exists.'],
            ]);
        }

        $data['vendor_id'] = Auth::user()->vendor_id;

        return new SocialLinkResource(SocialLink::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SocialLink  $socialLink
     * @return SocialLinkResource
     */
    public function show(SocialLink $socialLink)
    {
        if ($socialLink->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return new SocialLinkResource($socialLink);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SocialLink  $socialLink
     * @return bool
     */
    public function update(Request $request, SocialLink $socialLink)
    {
        if ($socialLink->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validate([
            'site' => 'required|string|in:Facebook,Youtube,Twitter,Instagram',
            'link' => 'required|max:255|url'
        ]);

        $exits = SocialLink::where('vendor_id', Auth::user()->vendor_id)
            ->where('site', $data['site'])
            ->where('id', '!=', $socialLink->id)
            ->first();

        if ($exits) {
            throw ValidationException::withMessages([
                'site' => ['The site already exists.'],
            ]);
        }

        return $socialLink->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SocialLink  $socialLink
     * @return bool
     */
    public function destroy(SocialLink $socialLink)
    {
        if ($socialLink->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return $socialLink->delete();
    }
}
