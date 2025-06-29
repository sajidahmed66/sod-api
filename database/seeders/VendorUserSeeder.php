<?php

namespace Database\Seeders;

use App\Models\VendorUser;
use Illuminate\Database\Seeder;

class VendorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VendorUser::create([
            'vendor_id' => 1,
            'type' => 1,
            'name' => 'Vendor User',
            'email' => 'vendor@gmail.com',
            'password' => bcrypt('123456')
        ]);
    }
}
