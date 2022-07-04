<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsuarioAdm;
use App\Models\UsuarioApp;
use Illuminate\Http\Request;

class CadastroUsuariosController extends Controller
{
    //

    public function cadastro_store(Request $request)
    {

        //
            $regras = [

                'nome' => 'required|min:4',
                'email' => 'email:rfc,dns|required|unique:users',
                'password' => 'required|min:4|max:10|confirmed',
                'status' => 'required',
                'nivel_acesso' => 'required',
                'foto_usuario' => 'required|file|mimes:png,jpg'

            ];


        $feedback = [

            'required' => 'O campo :attribute precisar ser prenchido',
            'email.email' => 'Digite um E-mail valido',
            'email.unique' => 'Email ja existe',
            'min' => 'O campo :attribute precisar ter no Minimo 4 caracter',
            'max' => 'O campo :attribute precisar ter no Maximo 10 caracter',
            'foto_usuario.mimes' => 'O formato do arquivo de ser PNG OU JPG',
            'password.confirmed' => 'Sua senha não são Indenticas'

        ];

        $request->validate($regras, $feedback);

        $senha = bcrypt($request->password);

        if($request->nivel_acesso === 'app'){
            // dd('app');
            $imagem = $request->file('foto_usuario');
            $imagem_urn = $imagem->store('imagens', 'public');

            $user = User::create([

                'email' => $request->email,
                'password' => $senha,
                'nivel_acesso' => $request->nivel_acesso,

            ]);

            $usuario_app = UsuarioApp::create([
                'user_id' => $user->id,
                'nome' => $request->nome,
                'sobrenome' => $request->sobrenome,
                'status' => $request->status,
                'foto_usuario' => $imagem_urn

            ]);
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

            return response()->json($usuario, 201);

        }
        else{
            return response()->json(['msg' => 'Seu nivel de acesso deve ser app'], 403);
        }


    }
}
