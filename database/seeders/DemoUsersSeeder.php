<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@rentease.com'],
            [
                'name'     => 'Demo Admin',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        // Lessor
        User::updateOrCreate(
            ['email' => 'lessor@rentease.com'],
            [
                'name'     => 'Demo Lessor',
                'password' => Hash::make('lessor123'),
                'role'     => 'lessor',
            ]
        );

        // Client
        User::updateOrCreate(
            ['email' => 'client@rentease.com'],
            [
                'name'     => 'Demo Client',
                'password' => Hash::make('client123'),
                'role'     => 'client',
            ]
        );
    }
}
