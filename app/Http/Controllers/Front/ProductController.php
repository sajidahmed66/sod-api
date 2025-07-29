<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function newProducts(Request $request)
    {
        $products = Product::where('vendor_id', $request->header('Vendor'))
            ->where('active', 1)
            ->latest()
            ->take(3)
            ->get();

        $products = $this->checkWishlist($products);
        return ProductResource::collection($products);
    }

    public function popularProducts(Request $request)
    {
        $data = [];
        $categories = Category::where('vendor_id', $request->header('Vendor'))
            ->limit(5)
            ->orderBy('sort')
            ->get();


        $data[] = [
            'id' => 0,
            'name' => 'All',
            'products' => ProductResource::collection(
                $this->checkWishlist(
                    Product::where('vendor_id', $request->header('Vendor'))
                        ->where('active', 1)
                        ->latest()
                        ->limit(10)
                        ->get()
                )
            )
        ];

        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->id,
                'name' => $category->name,
                'products' => ProductResource::collection(
                    $this->checkWishlist(
                        Product::where('category_id', $category->id)
                            ->where('active', 1)
                            ->latest()
                            ->limit(10)
                            ->get()
                    )
                )
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function hotProducts(Request $request)
    {
        $products = Product::where('vendor_id', $request->header('Vendor'))
            ->where('active', 1)
            ->whereNotNull('original_price')
            ->latest()
            ->get();

        return ProductResource::collection($products);
    }

    public function categoryProducts(Category $category, Request $request)
    {
        $query = Product::query()
            ->where('category_id', $category->id)
            ->where('active', 1);

        if ($request->sub) {
            $subCategoriesIds = explode(',', $request->sub);
            $query->whereIn('sub_category_id', $subCategoriesIds);
        }

        $products = executeQuery($query);
        $products = $this->checkWishlist($products);

        return ProductResource::collection($products);
    }

    public function details($slug, Request $request)
    {
        $product = Product::where('slug', $slug)
            ->where('vendor_id', $request->header('Vendor'))
            ->where('active', 1)
            ->first();

        if (!$product)
            return response()->json(['Not found.'], 404);

        $user = request()->user();

        if ($user) {
            $wishlist = Wishlist::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            if ($wishlist)
                $product->wishlist = true;
        }
        return new ProductResource($product->load('category'));
    }

    public function relatedProducts(Product $product, Request $request)
    {
        return ProductResource::collection(
            $this->checkWishlist(Product::where('category_id', $product->category_id)
                ->where('active', 1)
                ->where('vendor_id', $request->header('Vendor'))
                ->inRandomOrder()
                ->take(5)->get())
        );
    }

    public function searchProducts(Request $request)
    {
        $products = [];

        if ($request->q && strlen($request->q) > 2) {
            $products = Product::where('name', 'like', '%'.$request->q.'%')
                ->where('active', 1)
                ->where('vendor_id', $request->header('Vendor'))
                ->take(5)->get();
        }

        $products = $this->checkWishlist($products);

        return ProductResource::collection($products);
    }

    public function checkWishlist($products)
    {
        $user = request()->user();

        if ($user) {
            $wishlists = Wishlist::where('user_id', $user->id)
                ->whereIn('product_id', $products->pluck('id')->toArray())
                ->get();

            foreach ($products as $product) {
                $exists = $wishlists->where('product_id', $product->id)->count();

                if ($exists) {
                    $product->wishlist = true;
                } else {
                    $product->wishlist = false;
                }
            }
        }

        return $products;
    }
}
