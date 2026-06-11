<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Kategorijas')->insert([
            [
                'nosaukums' => 'Privātmājas',
                'slogan' => 'privatmaja',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nosaukums' => 'Dzīvokļi',
                'slogan' => 'dzivoklis',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nosaukums' => 'Lauku īpašumi',
                'slogan' => 'lauki',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
