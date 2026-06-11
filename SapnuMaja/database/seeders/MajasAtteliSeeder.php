<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajasAtteliSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Majas_atteli')->insert([
            // ========== Фото для дома 1 (Jūrmalas iela 15) ==========
            [
                'attels_fail' => '/imageshouse1/kitchen.jpg',
                'attelu_kartiba' => 2,
                'MajasID' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse1/living.jpg',
                'attelu_kartiba' => 3,
                'MajasID' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse2/kitchen.jpg',
                'attelu_kartiba' => 2,
                'MajasID' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse2/living.jpg',
                'attelu_kartiba' => 3,
                'MajasID' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse2/bethroom.jpg',
                'attelu_kartiba' => 4,
                'MajasID' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse3/kitchen.png',
                'attelu_kartiba' => 2,
                'MajasID' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse3/living.png',
                'attelu_kartiba' => 3,
                'MajasID' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse3/bethroom.png',
                'attelu_kartiba' => 4,
                'MajasID' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse4/kitchen.jpg',
                'attelu_kartiba' => 2,
                'MajasID' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse4/living.jpg',
                'attelu_kartiba' => 2,
                'MajasID' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse5/kitchen.jpg',
                'attelu_kartiba' => 2,
                'MajasID' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse5/living.jpg',
                'attelu_kartiba' => 2,
                'MajasID' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse5/bethroom.jpg',
                'attelu_kartiba' => 3,
                'MajasID' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse6/kitchen.png',
                'attelu_kartiba' => 2,
                'MajasID' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse6/living.png',
                'attelu_kartiba' => 3,
                'MajasID' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attels_fail' => '/imageshouse6/bethroom.png',
                'attelu_kartiba' => 4,
                'MajasID' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
          
           ]);
    }
}