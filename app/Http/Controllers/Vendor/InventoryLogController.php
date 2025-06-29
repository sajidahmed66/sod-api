<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Http\Requests\InventoryUpdateRequest;
use App\Http\Resources\InventoryLogResource;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Services\AccountService;
use App\Services\InventoryLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryLogController extends Controller
{
    private  $inventoryLogService;
    private $accountingService;
    public function __construct(InventoryLogService $inventoryLogService, AccountService $accountingService)
    {
        $this->inventoryLogService = $inventoryLogService;
        $this->accountingService = $accountingService;
    }
    public function saveLog(InventoryRequest $request)
    {
        $user = Auth::user();
        $log = $request->all();

        try {
            $product = Product::where('vendor_id', $user->vendor_id)->where('id', $log['product_id'])->first();
            $qty = $product->available_qty;

            if ($log['type'] == 'New Stock' || $log['type'] =='Returned') {
                $qty += $log['qty'];
            } else {
                $qty -= $log['qty'];
            }

            $product->update(['available_qty' => $qty]);
            $log['vendor_id'] = $user->vendor_id;
            $inventory = InventoryLog::create($log);

            if ($log['paid'] > 0){
                $accountingData = [
                    'vendor_id' => $user->vendor_id,
                    'product_id' => $product->id,
                    'inventory_id' => $inventory->id,
                    'date' => Carbon::now(),
                    'title' => $product->name,
                    'qty' => $log['qty'],
                    'amount' => $log['paid']
                ];

                if ($log['type'] == 'Sold') {
                    $accountingData['type'] = 'Income';
                } else {
                    $accountingData['type'] = 'Expense';
                }
                $this->accountingService->upsertFromInventory($accountingData, 'create');
            }

            return true;

        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }

    public function getAllInventoryLogs(Request $request)
    {
        $user = Auth::user();

        $query = InventoryLog::query();
        $query->where('vendor_id', $user->vendor_id)->get();

        return InventoryLogResource::collection(executeQuery($query));
    }

    public function getAllInventoryLog($id)
    {
        $user = Auth::user();
        $inventory = InventoryLog::where('vendor_id', $user->vendor_id)->where('id', $id)->first();

        return new InventoryLogResource($inventory);
    }

    public function updateInventoryLog(InventoryUpdateRequest $request, $id)
    {
        $user = Auth::user();

        return $this->inventoryLogService->updateInventoryLog($request, $id, $user->vendor_id);
    }

    public function deleteInventoryLog($id)
    {
        $user = Auth::user();
        return $this->inventoryLogService->deleteInventoryLog($id, $user->vendor_id);
    }
}
