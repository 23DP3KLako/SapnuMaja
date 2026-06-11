<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class MajaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $privatmajaId = DB::table('Kategorijas')->where('slogan', 'privatmaja')->first()->ID;
        $dzivoklisId = DB::table('Kategorijas')->where('slogan', 'dzivoklis')->first()->ID;
        $laukiId = DB::table('Kategorijas')->where('slogan', 'lauki')->first()->ID;
        
        DB::table('Majas')->insert([
            [
                'MajasID' => 1,
                'pilseta' => 'Jūrmala',
                'rajons' => 'Majori',
                'adrese' => 'Jūrmalas iela 15',
                'cena' => 480000,
                'platiba' => 220.50,
                'zemes_platiba' => 8.00,
                'istabu_skaits' => 5,
                'stavu_skaits' => 1,
                'celsanas_gads' => 2021,
                'stavoklis' => 'Jauna būve',
                'virsraksts' => 'Moderna villa Jūrmalā',
                'apraksts' => 'Eleganta vienstāva villa ar panorāmas logiem, platiem koka terasēm un vieglu pieeju jūrai.',
                'ipasibas' => 'Autostāvvieta, Kamīns, Kondicionieris',
                'KategorijasID' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'MajasID' => 2,
                'pilseta' => 'Rīga',
                'rajons' => 'Centrs',
                'adrese' => 'Brīvības iela 45',
                'cena' => 320000,
                'platiba' => 185.00,
                'zemes_platiba' => null,
                'istabu_skaits' => 4,
                'stavu_skaits' => 3,
                'celsanas_gads' => 2018,
                'stavoklis' => 'Labā stāvoklī',
                'virsraksts' => 'Luksusa penthouse centrā',
                'apraksts' => 'Modernākais penthouse Rīgas centrā ar skatu uz pilsētu.',
                'ipasibas' => 'Lifts, Apsardze, Pagraba stāvvieta',
                'KategorijasID' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'MajasID' => 3,
                'pilseta' => 'Sigulda',
                'rajons' => 'Sigulda',
                'adrese' => 'Siguldas iela 7',
                'cena' => 210000,
                'platiba' => 150.00,
                'zemes_platiba' => 25.00,
                'istabu_skaits' => 3,
                'stavu_skaits' => 2,
                'celsanas_gads' => 2015,
                'stavoklis' => 'Labā stāvoklī',
                'virsraksts' => 'Kluss lauku īpašums Siguldā',
                'apraksts' => 'Brīnišķīgs lauku īpašums ar skatu uz Siguldas ainavām.',
                'ipasibas' => 'Dārzs, Pirts, Autostāvvieta',
                'KategorijasID' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'MajasID' => 4,
                'pilseta' => 'Ādaži',
                'rajons' => 'Ādažu novads',
                'adrese' => 'Kanāla iela 6',
                'cena' => 280000,
                'platiba' => 151.50,
                'zemes_platiba' => 9.00,
                'istabu_skaits' => 3,
                'stavu_skaits' => 2,
                'celsanas_gads' => 2024,
                'stavoklis' => 'Jauna būve',
                'virsraksts' => 'Moderna villa Ādažos',
                'apraksts' => 'Eleganta divstavu villa ar panorāmas logiem, platiem koka terasēm un vieglu pieeju mežam.',
                'ipasibas' => 'Autostāvvietas, Kondicionieris',
                'KategorijasID' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'MajasID' => 5,
                'pilseta' => 'Rīga',
                'rajons' => 'Purvciems',
                'adrese' => 'Stirnu iela 22, 4. stāvs, 15. dzīvoklis',
                'cena' => 95000,
                'platiba' => 67.00,
                'zemes_platiba' => 0.00,
                'istabu_skaits' => 3,
                'stavu_skaits' => 7,
                'celsanas_gads' => 2021,
                'stavoklis' => 'Jaunā stāvoklī',
                'virsraksts' => 'Moderna dzīvoklis jaunajā projektā Rīgā',
                'apraksts' => 'Saulaina un moderna 3-istabu dzīvoklis jaunajā daudzstāvu ēkā Purvciemā. Dzīvoklī ir kvalitatīvs remonts, atvērtā plānojuma virtuve-viesistaba, divi guļamistabas un plaša lodžija ar pilsētas skatu. Ēkā ir lifts, apsargāta autostāvvieta un video novērošana.',
                'ipasibas' => 'Lifts, Lodžija, Apsargāta autostāvvieta, Video novērošana, Energoefektīva ēka, Internets',
                'KategorijasID' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'MajasID' => 6,
                'pilseta' => 'Ogre',
                'rajons' => 'Ogres novads',
                'adrese' => 'Priežu iela 5',
                'cena' => 245000,
                'platiba' => 185.00,
                'zemes_platiba' => 45.00,
                'istabu_skaits' => 3,
                'stavu_skaits' => 2,
                'celsanas_gads' => 2019,
                'stavoklis' => 'Lieliskā stāvoklī',
                'virsraksts' => 'Divstāvu māja klusā meža apkaimē',
                'apraksts' => 'Iespaidīga divstāvu māja priežu meža ielokā netālu no Ogres. Pirmajā stāvā atrodas plašā viesistaba ar kamīnu, moderna virtuve un ēdamistaba. Otrajā stāvā — ērta guļamistaba, divas vannas istabas un plaša terase ar skatu uz mežu. Īpašumā ietilpst garāža divām automašīnām.',
                'ipasibas' => 'Kamīns, Sauna, Garāža, Terase, Meža skats, Dārzs, Urbums, Apkure ar siltumsūkni',
                'KategorijasID' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],

            
        ]);
    }
}
