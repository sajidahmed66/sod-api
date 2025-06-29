<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return CategoryResource::collection(
            Category::where('vendor_id', $request->header('Vendor'))
                ->with('subCategories')
                ->orderBy('sort')->get()
        );
    }
}
