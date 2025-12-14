<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'price_regular'],
            ['value' => '15']
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'price_small'],
            ['value' => '10']
        );
    }
}