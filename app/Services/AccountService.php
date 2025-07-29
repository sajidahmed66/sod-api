<?php

namespace App\Services;
use App\Models\Accounting;
use App\Models\InventoryLog;
use App\Models\Product;
use Carbon\Carbon;
class AccountService
{
    public function save($data, $user)
    {
        try {
            $data['vendor_id'] = $user->vendor_id;
            Accounting::create($data);

            if (isset($data['product_id'])) {

                $product = Product::where('vendor_id', $user->vendor_id)->where('id', $data['product_id'])->first();
                $qty = $product->available_qty;

                if ($data['type'] == 'Income') {
                    $qty += $data['qty'];
                } else {
                    $qty -= $data['qty'];
                }

                $product->update(['available_qty' => $qty]);
            }

            return true;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function update($data, $id)
    {
        try {

            $accounting = Accounting::where('vendor_id', $data['vendor_id'])->where('id', $id)->first();

            if (isset($data['product_id'])) {
                $product = Product::where('vendor_id', $data['vendor_id'])->where('id', $data['product_id'])->first();
                $productQty = $product->available_qty;

                if ($data['type'] == 'Income') {
                    if ($accounting->type == 'Income') {
                        $productQty = ($productQty - $accounting->qty) + $data['qty'];
                    } else {
                        $productQty = ($productQty + $accounting->qty) + $data['qty'];
                    }
                } else {
                    if ($accounting->type == 'Income') {
                        $productQty = ($productQty - $accounting->qty) - $data['qty'];
                    } else {
                        $productQty = ($productQty + $accounting->qty) - $data['qty'];
                    }
                }

                $product->update(['available_qty' => $productQty]);
            }

            $accounting->update($data);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($id, $vendorId)
    {
        try {
            $accounting = Accounting::where('vendor_id', $vendorId)->where('id', $id)->first();

            if ($accounting->product_id != null) {

                $product = Product::where('vendor_id', $vendorId)->where('id', $accounting->product_id)->first();
                $productQty = $product->available_qty;

                if ($accounting->type == 'Income') {
                    $productQty = $productQty - $accounting->qty;
                } else {
                    $productQty = $productQty + $accounting->qty;
                }
            }

            $accounting->delete();

            return $product->update(['available_qty' => $productQty]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function upsertFromInventory($data, $type='create')
    {
        if ($type == 'create') {
            return Accounting::create($data);
        } else {
            $accounting = Accounting::where('inventory_id', $data['inventory_id'])->first();

            return $accounting->update($data);
        }
    }
}
