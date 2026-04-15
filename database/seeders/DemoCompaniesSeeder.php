<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class DemoCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        Company::updateOrCreate(
            ['name' => 'ABC Rentals Inc.'],
            [
                'owner_name' => 'Juan Dela Cruz',
                'email'      => 'juan@abc.com',
                'phone'      => '+63 912 345 6789',
                'address'    => 'Manila, Philippines',
                'status'     => 'active',
            ]
        );
    }
}
