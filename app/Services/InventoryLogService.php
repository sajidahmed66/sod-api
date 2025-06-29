<?php

namespace App\Services;
use App\Models\Accounting;
use App\Models\InventoryLog;
use App\Models\Product;
use Carbon\Carbon;
class InventoryLogService
{
    private $accountingService;

    public function __construct(AccountService $accountingService)
    {
        $this->accountingService = $accountingService;
    }
    public function saveLog($productId, $vendorId, $qty, $type = "New Stock", $orderId = null)
    {
        $product = Product::where('vendor_id', $vendorId)->where('id', $productId)->first();

        $data = [
            'date' => Carbon::now(),
            'vendor_id' => $vendorId,
            'product_id' => $productId,
            'order_id' => $orderId,
            'type' => $type,
            'unit_price' => $product->price,
            'qty' => $qty,
            'amount' => $product->price * $qty,
            'paid' => 0,
        ];

        $product = Product::where('vendor_id', $vendorId)->where('id', $productId)->first();
        $productQty = $product->available_qty;

        if ($type == 'New Stock' || $type =='Returned') {
            $productQty = $product->available_qty + $qty;
        } else {
            $productQty = $product->available_qty - $qty;
        }


        $product->update(['available_qty' => $productQty]);
        InventoryLog::create($data);
    }

    public function updateInventoryLog($request, $id, $vendorId)
    {
        try {
            $data = $request->all();

            $inventory = InventoryLog::where('vendor_id', $vendorId)->where('id', $id)->first();
            $product = Product::where('vendor_id', $vendorId)->where('id', $data['product_id'])->first();
            $productQty = $product->available_qty;

            if ($data['type'] == 'New Stock' || $data['type'] =='Returned') {

                if ($inventory->type == 'New Stock' || $inventory->type =='Returned') {
                    $productQty = ($productQty - $inventory->qty) + $data['qty'];
                } else {
                    $productQty = ($productQty + $inventory->qty) + $data['qty'];
                }
            } else {
                if ($inventory->type == 'New Stock' || $inventory->type =='Returned') {
                    $productQty = ($productQty - $inventory->qty) - $data['qty'];
                } else {
                    $productQty = ($productQty + $inventory->qty) - $data['qty'];
                }
            }

            $inventory->update($data);

            if ($data['paid'] > 0){
                $accountingData = [
                    'vendor_id' => $vendorId,
                    'product_id' => $product->id,
                    'inventory_id' => $inventory->id,
                    'date' => Carbon::now(),
                    'title' => $product->name,
                    'qty' => $data['qty'],
                    'amount' => $data['paid']
                ];

                if ($data['type'] == 'Sold') {
                    $accountingData['type'] = 'Income';
                } else {
                    $accountingData['type'] = 'Expense';
                }
                $this->accountingService->upsertFromInventory($accountingData, 'edit');
            }

            return $product->update(['available_qty' => $productQty]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    public function deleteInventoryLog($id, $vendorId)
    {
        try {
            $inventory = InventoryLog::where('vendor_id', $vendorId)->where('id', $id)->first();
            $product = Product::where('vendor_id', $vendorId)->where('id', $inventory->product_id)->first();
            $accounting = Accounting::where('inventory_id', $id)->first();
            $productQty = $product->available_qty;

            if ($inventory->type == 'New Stock' || $inventory->type =='Returned') {
                $productQty = $productQty - $inventory->qty;
            } else {
                $productQty = $productQty + $inventory->qty;
            }

            $inventory->delete();
            if ($accounting) {
                $accounting->delete();
            }

            return $product->update(['available_qty' => $productQty]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deleteInventoryLogByOrderId($orderId, $vendorId)
    {
        $inventoryLogs = InventoryLog::where('vendor_id', $vendorId)->where('order_id', $orderId)->get();

        foreach ($inventoryLogs as $inventoryLog) {
            $product = Product::where('vendor_id', $vendorId)->where('id', $inventoryLog->product_id)->first();
            $productQty = $product->available_qty + $inventoryLog->qty;
            $product->update(['available_qty' => $productQty]);
            $inventoryLog->delete();
        }
    }
}
