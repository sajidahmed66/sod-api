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

class CheckCourierECourierStatus implements ShouldQueue
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
        $client = new Client();
        $crawler = $client->request('GET', 'https://ecourier.com.bd/tracking/?tracking_no='.$this->order->courier_tracking_no);
        $node = $crawler->filter('.track-info ul')->filter('ul > li > div > b')->eq(0);

        $status = $node->count() ? $node->text() : null;

        if ($status && $status !== "NA") {
            Order::where('id', $this->order->id)->update([
                'courier_status' => $status
            ]);
        }
    }
}
