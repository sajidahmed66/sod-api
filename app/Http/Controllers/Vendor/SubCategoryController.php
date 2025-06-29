<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SubCategoryResource;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Category $category)
    {
        return SubCategoryResource::collection(executeQuery(SubCategory::query()->where('category_id', $category->id)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return SubCategoryResource
     */
    public function store(Category $category, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:199',
            'sort' => 'required|integer|min:1'
        ]);

        $data['vendor_id'] = Auth::user()->vendor_id;

        return new SubCategoryResource($category->subCategories()->save(new SubCategory($data)));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return SubCategoryResource
     */
    public function show(Category $category, SubCategory $subCategory)
    {
        if ($subCategory->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return new SubCategoryResource($subCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubCategory  $subCategory
     * @return bool
     */
    public function update(Request $request, Category $category, SubCategory $subCategory)
    {
        if ($subCategory->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validate([
            'name' => 'required|string|max:199',
            'sort' => 'required|integer|min:1'
        ]);

        return $subCategory->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return bool
     */
    public function destroy(Category $category, SubCategory $subCategory)
    {
        if ($subCategory->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return $subCategory->delete();
    }
}
