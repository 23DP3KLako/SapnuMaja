<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lietotajs;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Проверяем, существует ли уже админ
        $adminExists = Lietotajs::where('epasts', 'admin@example.com')->exists();
        
        if (!$adminExists) {
            Lietotajs::create([
                'lietotajvards' => 'admin',
                'epasts' => 'admin@example.lv',
                'parole' => Hash::make('admin123'),
                'loma' => 'admins',
                'statuss' => 'aktivs',
                
            ]);
            $this->command->info('Admin user created successfully!');
        } else {
            $this->command->info('Admin user already exists!');
        }
    }
}
