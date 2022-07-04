<?php

namespace App\Http\Controllers;

use App\Http\Middleware\LogUsuarioAppMiddleware;
use App\Models\User;
use App\Models\UsuarioAdm;
use App\Models\UsuarioApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {

        $user = User::where('email', $request->email)->first();
        
        if($user == null){
            return response()->json(['msg' => 'E-mail ou Senha incorreta'], 401);
        }


        if ($user->nivel_acesso === 'app') {

            //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
            $user = User::where('email', $request->email)->first();
            $usuario_app = $user->with('UsuarioApp')->find($user->id);

            if ($usuario_app->usuarioapp->status == 'ativo') {



                $credenciais = $request->all(['email', 'password']);

                $token = auth('api')->attempt($credenciais); //attempt ele tenta fazer autenticacao



                if ($token) { //usuario autenticado com sucesso

                    $user = User::where('email', $request->email)->first();
                    $usuario = $user->with('UsuarioApp')->find($user->id);

                    $usuario = array(
                        'id' => $usuario->id,
                        'email' => $usuario->email,
                        'nivel_acesso' => $usuario->nivel_acesso,
                        'nome' => $usuario->usuarioapp->nome,
                        'sobrenome' => $usuario->usuarioapp->sobrenome,
                        'status' => $usuario->usuarioapp->status,
                        'foto_usuario' => $usuario->usuarioapp->foto_usuario,
                    );

                    return response()->json(['token' => $token, 'usuario' => $usuario]);
                } else { //erro de usuario ou senha
                    return response()->json(['erro' => 'usuario ou senha invalido token'], 403); // 403 = forbidden-> proibido (login invalido)
                }
                //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
            } else {

                return response()->json(['erro' => 'Sua conta pode estar desativada entre em contato com suporte'], 403); // 403 = forbidden-> proibido (login invalido)
            }

        } else if ($user->nivel_acesso === 'adm') {


            $user = User::where('email', $request->email)->first();
            $usuario_adm = $user->with('UsuarioAdm')->find($user->id);


            if ($usuario_adm->usuarioadm->status == 'ativo') {


                $credenciais = $request->all(['email', 'password']);

                $token = auth('api')->attempt($credenciais); //attempt ele tenta fazer autenticacao


                if ($token) { //usuario autenticado com sucesso

                    $user = User::where('email', $request->email)->first();
                    $usuario = $user->with('UsuarioAdm')->find($user->id);

                    $usuario = array(
                        'id' => $usuario->id,
                        'email' => $usuario->email,
                        'nivel_acesso' => $usuario->nivel_acesso,
                        'nome' => $usuario->usuarioadm->nome,
                        'status' => $usuario->usuarioadm->status,
                    );


                    return response()->json(['token' => $token, 'usuario' => $usuario]);
                } else { //erro de usuario ou senha
                    return response()->json(['erro' => 'usuario ou senha invalido'], 403); // 403 = forbidden-> proibido (login invalido)
                }
            } else {

                return response()->json(['erro' => 'Sua conta pode estar desativada entre em contato com suporte'], 403); // 403 = forbidden-> proibido (login invalido)
            }
        }
    }
      /**
     * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     * logout usuario app
     * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     *
     */
    public function logoutUsuarioApp(Request $request){

        auth('api')->logout();
        return response()->json(['msg' => ' O logout foi realizado com sucesso']);

    }
 /**
     * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     * logout usuario app
     * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     *
     */
    public function logoutUsuarioAdm(Request $request){

        // dd('logout adm');
        auth('api')->logout();
        return response()->json(['msg' => ' O logout foi realizado com sucesso']);

    }


}
