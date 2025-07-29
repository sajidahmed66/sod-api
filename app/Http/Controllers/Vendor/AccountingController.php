<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountingRequest;
use App\Http\Resources\AccountResource;
use App\Models\Accounting;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Services\AccountService;
use App\Services\InventoryLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingController extends Controller
{
    private  $accountingService;

    public function __construct(AccountService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function getAccountingData(Request $request)
    {
        $user = Auth::user();
        $query = Accounting::query();
        $query->where('vendor_id', $user->vendor_id)->get();

        return AccountResource::collection(executeQuery($query));
    }

    public function save(AccountingRequest $request)
    {
        $user = Auth::user();
        $data = $request->all();

        return $this->accountingService->save($data, $user);
    }

    public function getSingleAccounting($id)
    {
        $user = Auth::user();
        $accounting= Accounting::where('vendor_id', $user->vendor_id)->where('id', $id)->first();

        return new AccountResource($accounting);
    }

    public function updateAccounting(AccountingRequest $request, $id)
    {
        $user = Auth::user();
        $data = $request->all();
        $data['vendor_id'] = $user->vendor_id;

        return $this->accountingService->update($data, $id);
    }

    public function deleteAccounting($id)
    {
        $user = Auth::user();
        return $this->accountingService->delete($id, $user->vendor_id);
    }
}
