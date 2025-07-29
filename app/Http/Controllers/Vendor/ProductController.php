<?php

namespace App\Http\Controllers\Vendor;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\InventoryLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    private  $inventoryLogService;

    public function __construct(InventoryLogService $inventoryLogService)
    {
        $this->inventoryLogService = $inventoryLogService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->active && $request->active != '')
            $query->where('active', $request->active);

        if ($request->stock_out && $request->stock_out != '')
            $query->where('stock_out', $request->stock_out);

        $query->where('products.vendor_id', Auth::user()->vendor_id)
            ->with('category', 'subCategory');


        return ProductResource::collection(executeQuery($query));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ProductResource
     */
    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        $data['active'] = $request->active && $request->active == "true";
        $data['stock_out'] = $request->stock_out && $request->stock_out == "true";
        $data['upcoming_product'] = $request->upcoming_product && $request->upcoming_product == "true";

        $data['vendor_id'] = Auth::user()->vendor_id;

        $variety = $data['variety'] ?? [];

        unset($data['variety']);

        if ($request->hasFile('image'))
            $data['image'] = resizeImage(Auth::user()->vendor_id, $request->file('image'), 500, 500, 'product_images');

        $product = Product::create($data);

        foreach ($variety as $item) {
            $pricesData = [
                'product_id' => $product->id,
                'name' => $item['name'],
                'price' => $item['price'],
                'original_price' => $item['original_price'] ?: null,
            ];

            ProductPrice::create($pricesData);
        }

        if ($data['available_qty'] > 0) {

            $this->inventoryLogService->saveLog($product->id, $data['vendor_id'], $data['available_qty']);
        }


        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return ProductResource
     */
    public function show(Product $product)
    {
        if ($product->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return new ProductResource($product->load('category',
            'subCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return bool
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        if ($product->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validated();

        $data['active'] = $request->active && $request->active == "true";
        $data['stock_out'] = $request->stock_out && $request->stock_out == "true";
        $data['upcoming_product'] = $request->upcoming_product && $request->upcoming_product == "true";

        if ($request->hasFile('image')) {
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }

            $data['image'] = resizeImage(Auth::user()->vendor_id, $request->file('image'), 500, 500, 'product_images');
        } else {
            unset($data['image']);
        }

        $variety = $data['variety'] ?? [];
        unset($data['variety']);

        $oldPriceIds = $product->prices->pluck('id');
        $newPriceIds = [];
        DB::beginTransaction();
        try {
            ProductPrice::where('product_id', $product->id)->delete();
            foreach ($variety as $item) {
                $pricesData = [
                    'product_id' => $product->id,
                    'name' => $item['name'],
                    'price' => $item['price'],
                ];
                if (isset($item['original_price']) && $item['original_price']) {
                    $pricesData['original_price'] = $item['original_price'];
                }
                ProductPrice::create($pricesData);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::alert($e->getMessage());
        }

        return $product->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return bool
     */
    public function destroy(Product $product)
    {
        if ($product->vendor_id != Auth::user()->vendor_id)
            return response()->json(['message' => 'Unauthorized'], 401);

        return $product->delete();
    }

    public function getPrices($product_id)
    {
        return ProductPrice::where('product_id', $product_id)->get();
    }

    public function downloadExcel(Request $request)
    {
        $startDate = Carbon::parse(substr($request->startTime, 1, -1));
        $endDate = Carbon::parse(substr($request->endTime, 1, -1));
        $status = $request->status;

        $startDate = $startDate->format('Y-m-d');
        $endDate = $endDate->format('Y-m-d');

        return Excel::download(new OrdersExport(Auth::user()->vendor_id, $startDate, $endDate, $status), 'orders.xlsx');
    }
}
