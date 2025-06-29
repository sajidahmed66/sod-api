<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaticPageResource;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaticPageController extends Controller
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
            'page_id' => 'required|integer|between:1,4',
            'content' => 'nullable|string|max:2000'
        ]);

        $page = StaticPage::where('vendor_id', Auth::user()->vendor_id)
            ->where('page_id', $data['page_id'])
            ->first();

        if (!$page) {
            $page = new StaticPage();
            $page->vendor_id = Auth::user()->vendor_id;
            $page->page_id = $data['page_id'];
        }

        $page->content = $data['content'];

        return $page->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StaticPage  $staticPage
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $page = StaticPage::where('vendor_id', Auth::user()->vendor_id)
            ->where('page_id', $id)
            ->first();

        if ($page)
            return new StaticPageResource($page);

        return response()->json(['message' => 'Not found.'], 404);
    }
}
