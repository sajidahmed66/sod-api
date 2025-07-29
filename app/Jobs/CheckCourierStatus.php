<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckCourierStatus
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sundarban = Order::where('courier_id', 1)
            ->whereNotNull('courier_tracking_no')
            ->where(function ($q) {
                $q->whereNull('courier_status');
                $q->orWhere(function($q2) {
                    $q2->where('courier_status', '!=', "Delivered");
                    $q2->where('courier_status', 'NOT LIKE', "Ready for C/D at%");
                });

            })
            ->get();

        $usb = Order::where('courier_id', 4)
            ->whereNotNull('courier_tracking_no')
            ->where(function ($q) {
                $q->whereNull('courier_status');
                $q->orWhere('courier_status', '!=', "Delivered");
            })
            ->get();

        $redx = Order::where('courier_id', 7)
            ->whereNotNull('courier_tracking_no')
            ->where(function ($q) {
                $q->whereNull('courier_status');
                $q->orWhere('courier_status', '!=', "Parcel is delivered successfully");
            })
            ->get();

        $ecourier = Order::where('courier_id', 8)
            ->whereNotNull('courier_tracking_no')
            ->where(function ($q) {
                $q->whereNull('courier_status');
                $q->orWhere('courier_status', '!=', "Delivered");
            })
            ->get();

        $steadFast = Order::where('courier_id', 13)
            ->whereNotNull('courier_tracking_no')
            ->where(function ($q) {
                $q->whereNull('courier_status');
                $q->orWhere('courier_status', '!=', "Delivery Status has been approved.");
            })
            ->get();

        foreach ($sundarban as $order) {
            CheckCourierSundarbanStatus::dispatch($order);
        }

        foreach ($redx as $order) {
            CheckCourierRedxStatus::dispatch($order);
        }

        foreach ($ecourier as $order) {
            CheckCourierECourierStatus::dispatch($order);
        }

        foreach ($usb as $order) {
            CheckCourierUSBStatus::dispatch($order);
        }

        foreach ($steadFast as $order) {
            CheckCourierSteadFast::dispatch($order);
        }
    }
}
