<?php

namespace App\Http\Controllers;

use App\Models\Lietotajs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AutenController extends Controller
{
    // Регистрация нового пользователя
    public function register(Request $request)
    {
        // Валидация данных
        $validator = Validator::make($request->all(), [
            'lietotajvards' => 'required|string|max:100|unique:Lietotajs',
            'epasts' => 'required|string|email|max:255|unique:Lietotajs',
            'parole' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Создаем пользователя (НЕ гость, а зарегистрированный пользователь)
        $lietotajs = Lietotajs::create([
            'lietotajvards' => $request->lietotajvards,
            'epasts' => $request->epasts,
            'parole' => Hash::make($request->parole),
            'statuss' => 'aktivs',
            'loma' => 'registrets'  // Устанавливаем роль как 'registrets' для новых пользователей
        ]);

        // Создаем простой токен
        $token = $lietotajs->kodsID;

       

        return response()->json([
            'success' => true,
            'message' => 'Reģistrācija veiksmīga',
            'token' => $token,
            'user' => [
                'kodsID' => $lietotajs->kodsID,
                'lietotajvards' => $lietotajs->lietotajvards,
                'epasts' => $lietotajs->epasts,
                'loma' => $lietotajs->loma,  // Теперь будет 'registrets'
                'statuss' => $lietotajs->statuss
            ]
        ], 201);
    }

    private function isAdmin($token)
    {
        $user = $this->getUserFromToken($token);
        return $user && $user->loma === 'admins';
    }

    // Вход пользователя
    public function login(Request $request)
    {
        // Валидация данных
        $validator = Validator::make($request->all(), [
            'epasts' => 'required|email',
            'parole' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Ищем пользователя по email
        $lietotajs = Lietotajs::where('epasts', $request->epasts)->first();

        // Проверяем пароль
        if (!$lietotajs || !Hash::check($request->parole, $lietotajs->parole)) {
            return response()->json([
                'success' => false,
                'message' => 'Nepareizs e-pasts vai parole'
            ], 401);
        }

        // Проверяем статус
        if ($lietotajs->statuss === 'blokets') {
            return response()->json([
                'success' => false,
                'message' => 'Jūsu konts ir bloķēts'
            ], 403);
        }

        // Создаем новый токен
        $token = $lietotajs->kodsID;
       

        return response()->json([
            'success' => true,
            'message' => 'Pierakstīšanās veiksmīga',
            'token' => $token,
            'user' => [
                'kodsID' => $lietotajs->kodsID,
                'lietotajvards' => $lietotajs->lietotajvards,
                'epasts' => $lietotajs->epasts,
                'loma' => $lietotajs->loma,
                'statuss' => $lietotajs->statuss
            ]
        ]);
    }

    // Выход пользователя
    public function logout(Request $request)
    {
       

        return response()->json([
            'success' => true,
            'message' => 'Izrakstīšanās veiksmīga'
        ]);
    }
}