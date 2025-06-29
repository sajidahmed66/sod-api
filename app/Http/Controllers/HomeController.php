<?php

namespace App\Http\Controllers;

use App\Jobs\CheckCourierStatus;
use App\Jobs\SendSms;
use App\Models\Area;
use App\Models\City;
use App\Models\Order;
use App\Models\SmsLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;

class HomeController extends Controller
{
    public function importAreas()
    {
        $cities = City::all();

        foreach ($cities as $city) {
            $response = Http::get('https://www.rokomari.com/shipping/areas?cityId='.$city->id);

            if ($response->successful()) {
                $areas = [];

                foreach ($response->json() as $area) {
                    $areas[] = [
                        'id' => $area['areaId'],
                        'city_id' => $city->id,
                        'name_en' => $area['area'],
                        'name_bn' => $area['areaBangla'],
                    ];
                }

                DB::table('areas')->insert($areas);
            } else {
                dd('stop');
            }
        }
    }

    public function test()
    {
        CheckCourierStatus::dispatch();

        return '';


        $crawler = $client->request('GET', 'https://redx.com.bd/track-global-parcel/?trackingId=21A810TU8404Z');


        dd($crawler);

        $crawler = $client->request('GET', 'http://103.3.227.172:4040/Default.aspx?Page=SearchByCNNumber');

        $form = $crawler->selectButton('ctl00_btnSearch')->form();

        $values = $form->getValues();
        $values['ctl00$txtCNNumber'] = "70202000066496";

        $crawler = $client->submit($form, $values);

        //$result = $crawler->filter('#ctl00_gvOrders > tr');

        $table = $crawler->filter('#ctl00_gvOrders')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        dd($table[1][4]);
    }

    public function pdf()
    {
        $order = Order::where('id', 25)->with('vendor.settings', 'area', 'city', 'items')->first();

//        dd($order);
        $data = [
            'order' => $order
        ];

//        dd(Storage::path($order->vendor->settings->logo));

        $pdf = LaravelMpdf::loadView('pdf.invoice', $data);
        return $pdf->download('invoice.pdf');
    }

    public function smsTest()
    {
        $sms = SmsLog::first();

        SendSms::dispatch($sms);
    }

    public function getLatLong()
    {
        $areas = Area::with('city')->get();

        foreach ($areas as $area) {
            if (!$area->lat) {
                $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $area->name_bn . "," . $area->city->name_bn,
                    'key' => "AIzaSyAnKKbnZogxI9jte1w5VhVfg0CyyZyJTzw"
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['results']) && count($data['results'])) {
                        $area->lat = $data['results'][0]['geometry']['location']['lat'];
                        $area->lng = $data['results'][0]['geometry']['location']['lng'];
                        $area->save();
                    }
                }
            }
        }
    }
}
