<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $couriers = [
            'Sundarban Courier Service (Pvt.) Ltd.',
            'SA Paribahan',
            'Janani Express Parcel Service',
            'USB Express',
            'Karatoa Courier Service',
            'Pathao Courier',
            'REDX Delivery',
            'eCourier',
            'Delivery Tiger',
            'Sonar Courier',
            'Sheba Delivery',
        ];

        foreach ($couriers as $courier) {
            Courier::create([
                'name' => $courier
            ]);
        }
    }
}
