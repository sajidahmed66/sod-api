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

class CheckCourierSteadFast implements ShouldQueue
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
        $response = Http::get('https://steadfast.com.bd/track/consignment/'.$this->order->courier_tracking_no);

        if ($response->successful()) {
            $data = $response->json();

            if (is_array($data) && count($data) === 5) {
                if (is_array($data[4]) && count($data[4])) {
                    Order::where('id', $this->order->id)->update([
                        'courier_status' => $data[4][0]['text']
                    ]);
                }
            }
        }

    }
}
