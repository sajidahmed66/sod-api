<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Area;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Vendor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function topWidgets()
    {
        $orders = Order::where('vendor_id', Auth::user()->vendor_id)
//            ->whereBetween('created_at', [Carbon::now()->subDays(30)->startOfDay(), Carbon::now()])
            ->whereIn('status', ["Confirmed", "Shipped", "Completed"])
            ->get();

        $vendor = Vendor::find(Auth::user()->vendor_id);

        $areas = Area::all();

        $markers = [];

        foreach ($areas as $area) {
            $count = $orders->where('area_id', $area->id)->count();

            if ($count) {
                $markers[] = [
                    'lat' => $area->lat,
                    'lng' => $area->lng,
                    'count' => $count."",
                    'title' => $area->name_bn
                ];
            }
        }

        return response()->json([
            'data' => [
                'orderCount' => $orders->count(),
                'sales' => '৳'.number_format($orders->sum('total'), 2),
                'sms' => $vendor->sms_balance,
                'due' => '৳'.number_format($orders->where('due', '>', 0)->sum('due'), 2),
                'markers' => $markers
            ]
        ]);
    }

    public function salesChart()
    {
        $orders = Order::where('vendor_id', Auth::user()->vendor_id)
            ->whereBetween('created_at', [Carbon::now()->subMonths(11)->startOfMonth()->startOfDay(), Carbon::now()])
            ->whereIn('status', ["Confirmed", "Shipped", "Completed"])
            ->get();

        $startDate = Carbon::now()->subMonths(11);

        $data = [];

        for ($i=11; $i >= 0; $i--) {
            $orderByMonth = $orders->whereBetween('created_at',
                [Carbon::now()->subMonths($i)->startOfMonth()->startOfDay(), Carbon::now()->subMonths($i)->endOfMonth()->endOfDay()]);

            $data[] = [
                'label' => Carbon::now()->subMonths($i)->format('M'),
                'count' => $orderByMonth->count(),
                'sales' => $orderByMonth->sum('total')
            ];
        }
        return response()->json([
            'data' => $data
        ]);
    }

    public function getAllByDateRange(Request $request)
    {
        $vendorId = Auth::user()->vendor_id;

        $date = $request->all();
        $date['startDate'] = $date['selectedStartDate'];
        $date['endDate'] = $date['selectedEndDate'];

        $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' +1 day'));
        $areas = Area::all();
        $vendor = Vendor::find(Auth::user()->vendor_id);

        if ($date['startDate'] == null) {
            $firstRecord = Order::where('vendor_id', $vendorId)->orderBy('created_at', 'asc')->first();
            $date['startDate'] = date('Y-m-d', strtotime($firstRecord->created_at . ' -1 day'));
//            dd($firstRecord->created_at, $date['startDate']);
        }

        $orders = Order::where('vendor_id', $vendorId)->where('created_at' , '>=', $date['startDate'])->where('created_at' , '<=', $date['endDate'])->get();

        $accounting = Accounting::selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as totalIncome, SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as totalExpense')
            ->where('vendor_id', $vendorId)->where('created_at' , '>=', $date['startDate'])->where('created_at' , '<=', $date['endDate'])->get();
        $income = $accounting[0]->totalIncome;
        $expanse = $accounting[0]->totalExpense;

        $period = CarbonPeriod::create($date['startDate'], '1 month', $date['endDate']);
        $monthsCount = iterator_count($period);

        $topCardData = $this->topCardData($vendorId);
        $completedOrders = 0;
        $cancelOrders = 0;
        $totalSaleAmount = 0;
        $paidAmount = 0;
        $dueAmount = 0;
        $markers = [];
        $ordersCount = 0;
        $orderIds = [];
        foreach ($orders as $order) {
            $orderIds[] = $order['id'];
            if ($order->status == 'Completed') {
                $completedOrders++;
            }
            if ($order->status == 'Cancelled') {
                $cancelOrders++;
            }
            $totalSaleAmount+= $order->total;
            $paidAmount+= $order->paid;
            $dueAmount+= $order->due;
            ++$ordersCount;
        }

        foreach ($areas as $area) {
            $count = $orders->where('area_id', $area->id)->count();

            if ($count) {
                $markers[] = [
                    'lat' => $area->lat,
                    'lng' => $area->lng,
                    'count' => $count."",
                    'title' => $area->name_bn
                ];
            }
        }

        $topSoldProducts = OrderItem::whereIn('order_id', $orderIds)
            ->select('product_name', 'product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_name', 'product_id')
            ->orderBy('total_quantity', 'desc')
            ->take(3)
            ->get();

        $data = [
            'pendingCount' => $topCardData['pending'],
            'confirmedCount' => $topCardData['confirmed'],
            'shippedCount' => $topCardData['shipped'],
            'completeCount' => $completedOrders,
            'cancelCount' => $cancelOrders,
            'paid' => $paidAmount,
            'monthCount' => $monthsCount,
            'topProducts' => $topSoldProducts,
            'income' => $income,
            'expense' => $expanse,
            'topWidget' => [
                'markers' => $markers,
                'orderCount' => $ordersCount,
                'sales' => $totalSaleAmount,
                'due' => $dueAmount,
                'sms' => $vendor->sms_balance,
            ]

        ];

        $orders = $orders->whereIn('status', ["Confirmed", "Shipped", "Completed"]);

        $chartData = [];

        for ($i = $monthsCount; $i >= 0; $i--) {
            $orderByMonth = $orders->whereBetween('created_at',
                [Carbon::parse($date['endDate'])->subMonths($i)->startOfMonth()->startOfDay(), Carbon::parse($date['endDate'])->subMonths($i)->endOfMonth()->endOfDay()]);

            $chartData[] = [
                'label' => Carbon::parse($date['endDate'])->subMonths($i)->format('M'),
                'count' => $orderByMonth->count(),
                'sales' => $orderByMonth->sum('total')
            ];
        }
        $data['salesChart'] = $chartData;


        return response()->json([
            'data' => $data
        ]);
    }

    public function topCardData($vendorId)
    {
        $orders = Order::where('vendor_id', $vendorId)->get();

        return [
            'pending' => count($orders->where('status', 'Pending')),
            'confirmed' => count($orders->where('status', 'Confirmed')),
            'shipped' => count($orders->where('status', 'Shipped'))
        ];
    }
}
