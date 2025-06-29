<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckCourierRedxStatus
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
        $response = Http::withHeaders([
            'Accept' => '*/*',
            'Host' => 'api.redx.com.bd',
            'User-Agent' => 'PostmanRuntime/7.29.0',
        ])->get('https://api.redx.com.bd/v1/logistics/global-tracking/'.$this->order->courier_tracking_no);

        if ($response->successful()) {
            $data = $response->json();

            if (!$data['isError']) {
                $tracking = $data['tracking'];

                Order::where('id', $this->order->id)->update([
                    'courier_status' => $tracking[count($tracking)-1]['messageEn']
                ]);
            }
        }
    }
}
