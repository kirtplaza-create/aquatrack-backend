<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate( // create or update admin user
            ['name' => 'admin'],   // this will be the username
            [
                'email' => 'owner@example.com', // optional, for contact only
                'password' => Hash::make('123456'),
                'phone' => '09460000000',
                'address' => 'Butuan City',
            ]
        );
    }
}