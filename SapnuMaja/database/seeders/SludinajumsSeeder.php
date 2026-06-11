<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SludinajumsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Sludinajums')->insert([
            [
                'SludinajumsID' => 1,
                'attels' => '/images/house1.jpg',
                'MajasID' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'SludinajumsID' => 2,
                'attels' => '/images/house2.jpg',
                'MajasID' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'SludinajumsID' => 3,
                'attels' => '/images/house3.jpg',
                'MajasID' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'SludinajumsID' => 4,
                'attels' => '/images/house4.jpg',
                'MajasID' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'SludinajumsID' => 5,
                'attels' => '/images/house5.jpg',
                'MajasID' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'SludinajumsID' => 6,
                'attels' => '/images/house6.jpg',
                'MajasID' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}