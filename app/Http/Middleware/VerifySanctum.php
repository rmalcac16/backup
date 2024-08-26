<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class VerifySanctum
{
    public function handle($request, Closure $next) 
    {
        
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token no proporcionado'
            ], 200);
        }

        // Verificar si el Bearer Token es válido
        if (!auth('sanctum')->check($token)) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'msg' => 'Token inválido, Por favor inicie sesion nuevamente'
            ], 200);
        }

        return $next($request);
    }
}