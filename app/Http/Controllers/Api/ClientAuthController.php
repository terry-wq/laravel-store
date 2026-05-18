<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientAuthController extends Controller
{
    private function response($success, $message, $data = null, $status = 200, $errors = null)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'errors' => $errors
        ], $status);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:clients,email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return $this->response(
                false,
                'Los datos enviados no son válidos',
                null,
                422,
                $validator->errors()
            );
        }

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'api_token' => Str::random(60)
        ]);

        return $this->response(
            true,
            'Registro exitoso',
            [
                'token' => $client->api_token,
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email
                ]
            ],
            201
        );
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response(
                false,
                'Los datos enviados no son válidos',
                null,
                422,
                $validator->errors()
            );
        }

        $client = Client::where('email', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            return $this->response(
                false,
                'Correo o contraseña incorrectos',
                null,
                401
            );
        }

        $client->api_token = Str::random(60);
        $client->save();
        $client->refresh();

        return $this->response(
            true,
            'Inicio de sesión exitoso',
            [
                'token' => $client->api_token,
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email
                ]
            ]
        );
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->response(false, 'No autorizado', null, 401);
        }

        $client = Client::where('api_token', $token)->first();

        if (!$client) {
            return $this->response(false, 'Sesión inválida', null, 401);
        }

        $client->api_token = null;
        $client->save();

        return $this->response(true, 'Sesión cerrada correctamente');
    }
}