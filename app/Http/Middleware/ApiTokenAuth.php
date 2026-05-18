<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Client;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (!$header) {
            return response()->json([
                'success' => false,
                'message' => 'No token proporcionado',
                'data' => null
            ], 401);
        }

        // 🔥 QUITAR "Bearer "
        $token = str_replace('Bearer ', '', $header);

        $client = Client::where('api_token', $token)->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido',
                'data' => null
            ], 401);
        }

        // 👇 inyectar cliente correctamente
        $request->merge(['client' => $client]);

        return $next($request);
    }
}