<?php

namespace App\Jobs;

use App\Models\Order;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckCourierSundarbanStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;

        $this->onQueue('check_courier');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::post('https://tracking.sundarbancourierltd.com/Home/getDatabyCN', [
            'inputvalue' => $this->order->courier_tracking_no,
            'selectedtimes' => "30",
            'selectedtypes' => "cnno",
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (count($data)) {
                Order::where('id', $this->order->id)->update([
                    'courier_status' => $data[0]['status']
                ]);
            }
        }
    }
}
