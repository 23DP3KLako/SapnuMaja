<?php

namespace App\Http\Controllers;

use App\Models\LietotajMajas;
use App\Models\Sludinajums;
use App\Models\Lietotajs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
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
    
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $user = $this->getUserFromToken($token);
        
        if (!$user) {
            return response()->json([]);
        }
        
        $favoriteIds = LietotajMajas::where('LietotajsID', $user->kodsID)
            ->whereNotNull('SludinajumsID') 
            ->pluck('SludinajumsID')
            ->toArray();
        
        if (empty($favoriteIds)) {
            return response()->json([]);
        }
        
       $favorites = DB::table('Sludinajums')
        ->join('Majas', 'Sludinajums.MajasID', '=', 'Majas.MajasID')
        ->join('Kategorijas', 'Majas.KategorijasID', '=', 'Kategorijas.ID')
        ->whereIn('Sludinajums.SludinajumsID', $favoriteIds)
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

        return response()->json($favorites);
    }
    
   public function store(Request $request)
{
    $token = $request->bearerToken();
    $user = $this->getUserFromToken($token);
    
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    $sludinajumsId = $request->input('sludinajums_id');
    
    if (!$sludinajumsId) {
        return response()->json(['error' => 'sludinajums_id is required'], 400);
    }

    try {
        $sludinajums = Sludinajums::find($sludinajumsId);
        if (!$sludinajums) {
            return response()->json(['error' => 'Sludinajums not found', 'id' => $sludinajumsId], 404);
        }
        
        $exists = LietotajMajas::where('LietotajsID', $user->kodsID)
            ->where('SludinajumsID', $sludinajumsId)
            ->exists();
            
        if ($exists) {
            return response()->json(['message' => 'Already in favorites'], 400);
        }
        
        $favorite = LietotajMajas::create([
            'LietotajsID' => $user->kodsID,
            'MajasID' => $sludinajumsId,
            'SludinajumsID' => $sludinajumsId,
            'statuss' => 'var nopirkt'
        ]);
        
        return response()->json(['success' => true], 201);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
}
    
    public function destroy($id, Request $request)
    {
        $token = $request->bearerToken();
        $user = $this->getUserFromToken($token);
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $favorite = LietotajMajas::where('LietotajsID', $user->kodsID)
            ->where('SludinajumsID', $id)
            ->first();
            
        if (!$favorite) {
            return response()->json(['message' => 'Not found'], 404);
        }
        
        $favorite->delete();
        
        return response()->json(['success' => true, 'message' => 'Removed from favorites']);
    }
    
    public function check($id, Request $request)
    {
        $token = $request->bearerToken();
        $user = $this->getUserFromToken($token);
        
        if (!$user) {
            return response()->json(['is_favorite' => false]);
        }
        
        $exists = LietotajMajas::where('LietotajsID', $user->kodsID)
            ->where('SludinajumsID', $id)
            ->exists();
            
        return response()->json(['is_favorite' => $exists]);
    }
}