<?php

namespace App\Http\Controllers;


use App\Models\Maja;
use App\Models\Kategorijas; 
use App\Models\Sludinajums;
use App\Models\Lietotajs;
use App\Models\MajasAtteli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MajaController extends Controller
{
    private function getUserFromToken($token)
    {
        if (!$token) {
            return null;
        }
        
        $token = str_replace('Bearer ', '', $token);
        $userId = (int)$token;
        
        return Lietotajs::find($userId);
    }

    private function isAdmin($token)
    {
        $user = $this->getUserFromToken($token);
        return $user && $user->loma === 'admins';
    }

    // Получить все дома с категориями
    public function index()
    {
        $sludinajumi = DB::table('Sludinajums')
            ->join('Majas', 'Sludinajums.MajasID', '=', 'Majas.MajasID')
            ->join('Kategorijas', 'Majas.KategorijasID', '=', 'Kategorijas.ID')
            ->select(
                'Sludinajums.SludinajumsID',
                'Sludinajums.attels',
                'Majas.MajasID',
                'Majas.adrese',
                'Majas.pilseta',
                'Majas.cena',
                'Kategorijas.ID as kategorija_id',
                'Kategorijas.slogan as cat_slogan'
            )
            ->get();
        
        return response()->json($sludinajumi);
    }
    
    // Получить один дом по ID
    public function show($id)
    {
        $sludinajums =DB::table('Sludinajums')
        ->join('Majas', 'Sludinajums.MajasID', '=', 'Majas.MajasID')
            ->join('Kategorijas', 'Majas.KategorijasID', '=', 'Kategorijas.ID')
            ->where('Sludinajums.SludinajumsID', $id)
            ->select(
                'Sludinajums.SludinajumsID',
                'Sludinajums.attels',
                'Majas.MajasID',
                'Majas.pilseta',
                'Majas.rajons',
                'Majas.adrese',
                'Majas.cena',
                'Majas.platiba',
                'Majas.zemes_platiba',
                'Majas.istabu_skaits',
                'Majas.stavu_skaits',
                'Majas.celsanas_gads',
                'Majas.stavoklis',
                'Majas.virsraksts',
                'Majas.apraksts',
                'Majas.ipasibas',
                'Kategorijas.nosaukums as category_name',
                'Kategorijas.slogan as cat_slogan'
            )
            ->first();
        
        if (!$sludinajums) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        $atteli = DB::table('Majas_atteli')
            ->where('MajasID', $sludinajums->MajasID)
            ->orderBy('attelu_kartiba')
            ->get();
        
        $sludinajums->atteli = $atteli;
        
        return response()->json($sludinajums);
    }
    
    // Получить все категории
    public function kategorijas()
    {
        $kategorijas = Kategorijas::select('ID as id', 'nosaukums', 'slogan')->get();
   
        return response()->json($kategorijas);
    }

    public function store(Request $request)
    {
        $token = $request->bearerToken();
        if (!$this->isAdmin($token)) {
            return response()->json(['error' => 'Nav avtorizets ka admins'], 403);
        }

       

        try {
            // 1. Создаем запись в Majas
            $majaId = DB::table('Majas')->insertGetId([
                'pilseta' => $request->pilseta,
                'rajons' => $request->rajons,
                'adrese' => $request->adrese,
                'cena' => $request->cena,
                'platiba' => $request->platiba,
                'zemes_platiba' => $request->zemes_platiba,
                'istabu_skaits' => $request->istabu_skaits,
                'stavu_skaits' => $request->stavu_skaits,
                'celsanas_gads' => $request->celsanas_gads,
                'stavoklis' => $request->stavoklis,
                'virsraksts' => $request->virsraksts,
                'apraksts' => $request->apraksts,
                'ipasibas' => $request->ipasibas,
                'KategorijasID' => $request->KategorijasID,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // 2. Создаем запись в Sludinajums
            $sludinajumsId = DB::table('Sludinajums')->insertGetId([
                'attels' => $request->attels,
                'MajasID' => $majaId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $extraImages = $request->extra_images ?? [];
                foreach ($extraImages as $index => $imageUrl) {
                    if ($imageUrl && trim($imageUrl) !== '') {
                        DB::table('Majas_atteli')->insert([
                            'attels_fail' => $imageUrl,
                            'attelu_kartiba' => $index + 1,
                            'MajasID' => $majaId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            
            return response()->json([
                'success' => true,
                'message' => 'Īpašums pievienots!',
                'SludinajumsID' => $sludinajumsId
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ОБНОВИТЬ ОБЪЯВЛЕНИЕ (только админ)
    public function update(Request $request, $id)
    {
        $token = $request->bearerToken();
        if (!$this->isAdmin($token)) {
            return response()->json(['error' => 'Nav avtorizets ka admins'], 403);
        }

        try {
            // Находим объявление
            $sludinajums = DB::table('Sludinajums')->where('SludinajumsID', $id)->first();
            if (!$sludinajums) {
                return response()->json(['error' => 'Property not found'], 404);
            }
            
            // Обновляем Majas
            DB::table('Majas')->where('MajasID', $sludinajums->MajasID)->update([
                'pilseta' => $request->pilseta,
                'rajons' => $request->rajons,
                'adrese' => $request->adrese,
                'cena' => $request->cena,
                'platiba' => $request->platiba,
                'zemes_platiba' => $request->zemes_platiba,
                'istabu_skaits' => $request->istabu_skaits,
                'stavu_skaits' => $request->stavu_skaits,
                'celsanas_gads' => $request->celsanas_gads,
                'stavoklis' => $request->stavoklis,
                'virsraksts' => $request->virsraksts,
                'apraksts' => $request->apraksts,
                'ipasibas' => $request->ipasibas,
                'KategorijasID' => $request->KategorijasID,
                'updated_at' => now()
            ]);
            
            // Обновляем Sludinajums
            DB::table('Sludinajums')->where('SludinajumsID', $id)->update([
                'attels' => $request->attels,
                'updated_at' => now()
            ]);

            DB::table('Majas_atteli')->where('MajasID', $sludinajums->MajasID)->delete();

            $extraImages = $request->extra_images ?? [];
            $sortOrder = 1;
            foreach ($extraImages as $imageUrl) {
                if ($imageUrl && trim($imageUrl) !== '') {
                    DB::table('Majas_atteli')->insert([
                        'attels_fail' => $imageUrl,
                        'attelu_kartiba' => $sortOrder,
                        'MajasID' => $sludinajums->MajasID,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $sortOrder++;
                }
            }
            
            return response()->json(['success' => true, 'message' => 'Īpašums atjaunināts!']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

     // УДАЛИТЬ ОБЪЯВЛЕНИЕ (только админ)
    public function destroy(Request $request,$id)
    {
        $token = $request->bearerToken();
        if (!$this->isAdmin($token)) {
            return response()->json(['error' => 'Nav avtorizets ka admins'], 403);
        }

        try {
            $sludinajums = DB::table('Sludinajums')->where('SludinajumsID', $id)->first();
            if (!$sludinajums) {
                return response()->json(['error' => 'Property not found'], 404);
            }
            
            // Удаляем фото
            DB::table('Majas_atteli')->where('MajasID', $sludinajums->MajasID)->delete();
            // Удаляем объявление
            DB::table('Sludinajums')->where('SludinajumsID', $id)->delete();
            // Удаляем дом
            DB::table('Majas')->where('MajasID', $sludinajums->MajasID)->delete();
            
            return response()->json(['success' => true, 'message' => 'Īpašums dzēsts!']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Добавить категорию (только админ)
    public function storeCategory(Request $request)
    {
        $token = $request->bearerToken();
        if (!$this->isAdmin($token)) {
            return response()->json(['error' => 'Nav avtorizets ka admins'], 403);
        }
        
        try {
            $id = DB::table('Kategorijas')->insertGetId([
                'nosaukums' => $request->nosaukums,
                'slogan' => $request->slogan,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Kategorija pievienota!', 'ID' => $id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Удалить категорию (только админ)
    public function destroyCategory(Request $request, $id)
    {
            $token = $request->bearerToken();
            if (!$this->isAdmin($token)) {
                return response()->json(['error' => 'Nav autorizēts kā admins'], 403);
            }
            
            // Добавьте проверку, что id не пустой
            if (!$id || $id === 'undefined') {
                return response()->json(['error' => 'Nepareizs kategorijas ID'], 400);
            }
            
            try {
                // Проверяем, существует ли категория перед удалением
                $category = DB::table('Kategorijas')->where('ID', $id)->first();
                
                if (!$category) {
                    return response()->json(['error' => 'Kategorija nav atrasta'], 404);
                }

                DB::table('Kategorijas')->where('ID', $id)->delete();
                
                return response()->json(['success' => true, 'message' => 'Kategorija dzēsta!']);
                
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
    }
}
