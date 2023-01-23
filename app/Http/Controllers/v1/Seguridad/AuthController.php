<?php

namespace App\Http\Controllers\v1\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Pacient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $userTemp = User::where('email', $request->email)->first();
            if (!$userTemp) {
                return response([
                    "status" => "500",
                    'message' => 'El usuario no existe',
                    'type' => 'error'
                ]);
            }
            if ($userTemp->tipo_user != 'interested'&&$userTemp->tipo_user != 'client'&&$userTemp->tipo_user != 'asesor'&&$userTemp->tipo_user != 'driver') {
                return response([
                    "status" => "500",
                    'message' => 'Usuario no permitido',
                    'type' => 'error'
                ]);
            }
            $credentials = [
                'email' => $request['email'],
                'password' => $request['password'],
            ];
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('app')->accessToken;
                return response([
                    "status" => "200",
                    'message' => 'Inicio de sesiòn correcto',
                    'token' => $token,
                    'user' => $user
                ]);
            } else {
                return response([
                    "status" => "500",
                    'message' => 'Credenciales incorrectas',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            return json_encode([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    public function loginAdmin(Request $request)
    {
        try {
            $userTemp = User::where('email', $request->email)->first();
            if (!$userTemp) {
                return response([
                    "status" => "500",
                    'message' => 'El usuario no existe',
                    'type' => 'error'
                ]);
            }
            if ($userTemp->tipo_user != 'admin') {
                return response([
                    "status" => "500",
                    'message' => 'Usuario no permitido',
                    'type' => 'error'
                ]);
            }
            $credentials = [
                'email' => $request['email'],
                'password' => $request['password'],
            ];
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('app')->accessToken;
                return response([
                    "status" => "200",
                    'message' => 'Inicio de sesiòn correcto',
                    'token' => $token,
                    'user' => $user
                ]);
            } else {
                return response([
                    "status" => "500",
                    'message' => 'Credenciales incorrectas',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            return json_encode([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    public function logout()
    {
        try {
            $user = Auth::user()->token();
            $user->revoke();
            return json_encode([
                "status" => "200",
                "message" => 'Sesión finalizada correctamente',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
}
