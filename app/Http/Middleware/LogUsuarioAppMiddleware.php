<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class LogUsuarioAppMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $user = auth('api')->user();
        if($user){

        $usuario_app = User::with('UsuarioApp')->find($user->id);

        if($usuario_app->nivel_acesso === 'app' && $usuario_app->usuarioapp->status === 'ativo') {
            return $next($request);
        }
        }
        else {

            return response()->json(['msg' => 'Token invalido OU Expirado'], 401);
        }
    }
}
