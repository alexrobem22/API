<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class LogUsuarioAdmMiddleware
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

            $usuario_app = User::with('UsuarioAdm')->find($user->id);

            if($usuario_app->nivel_acesso === 'adm' && $usuario_app->usuarioadm->status === 'ativo') {
                return $next($request);
            }
        }
        else {

            return response()->json(['msg' => 'Token invalido OU Expirado'], 401);
        }
    }
}
