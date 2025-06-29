<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CategoryResource::collection(executeQuery(Category::query()->where('vendor_id', Auth::user()->vendor_id)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return CategoryResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:199',
            'sort' => 'required|integer|min:1'
        ]);

        $data['vendor_id'] = Auth::user()->vendor_id;

        return new CategoryResource(Category::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return CategoryResource
     */
    public function show(Category $category)
    {
        if ($category->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return bool
     */
    public function update(Request $request, Category $category)
    {
        if ($category->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validate([
            'name' => 'required|string|max:199',
            'sort' => 'required|integer|min:1'
        ]);

        return $category->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return bool
     */
    public function destroy(Category $category)
    {
        if ($category->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return $category->delete();
    }
}
