<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class OrdersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $vendorId, $startDate, $endDate, $status;

    public function __construct($vendorId, $startDate, $endDate, $status)
    {
        $this->vendorId = $vendorId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function collection()
    {
        $status = $this->status;
        return Order::where('vendor_id', $this->vendorId)->where('created_at', '>=', $this->startDate)->where('created_at', '<=', $this->endDate)
            ->where(function ($q) use ($status) {
                if ($status) {
                    $q->where('status', $status);
                }
            })->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Order Number',
            'Customer Name',
            'Customer Mobile',
            'Customer Address',
            'Note',
            'Payment',
            'Sub Total',
            'Shipping Cost',
            'Discount',
            'Total',
            'Paid',
            'Due',
            'Status',
            'Courier Id',
            'Courier Branch',
            'Courier Tracking No',
            'Courier Status'
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at,
            $row->order_no,
            $row->name,
            $row->mobile,
            $row->address,
            $row->note,
            $row->payment,
            $row->sub_total,
            $row->shipping_cost,
            $row->discount,
            $row->total,
            $row->paid,
            $row->due,
            $row->status,
            $row->courier_id,
            $row->courier_branch,
            $row->courier_tracking_no,
            $row->courier_status,
        ];
    }
}
