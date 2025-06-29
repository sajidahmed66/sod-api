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

class CheckCourierUSBStatus implements ShouldQueue
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
        $crawler = $client->request('GET', 'http://45.251.58.170:7676/Default.aspx?Page=SearchByCNNumber');

        $form = $crawler->selectButton('ctl00_btnSearch')->form();

        $values = $form->getValues();
        $values['ctl00$txtCNNumber'] = $this->order->courier_tracking_no;

        $crawler = $client->submit($form, $values);

        $table = $crawler->filter('#ctl00_gvOrders')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td) {
                return trim($td->text());
            });
        });

        if (count($table) > 1) {
            Order::where('id', $this->order->id)->update([
                'courier_status' => $table[1][4]
            ]);
        }
    }
}
