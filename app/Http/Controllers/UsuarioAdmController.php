<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsuarioAdm;
use Illuminate\Http\Request;

class UsuarioAdmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
            $limit = $request->limit;

            $user = user::where('nivel_acesso', 'adm');
            $usuario = $user->with('UsuarioAdm')->paginate($limit);

        return response()->json($usuario, 201);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // dd('usuario adm');
        $regras = [

            'nome' => 'required|min:4',
            'email' => 'email:rfc,dns|required|unique:users',
            'password' => 'required|min:4|max:10|confirmed',
            'status' => 'required',
            'nivel_acesso' => 'required',


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

        $user = User::create([

            'email' => $request->email,
            'password' => $senha,
            'nivel_acesso' => $request->nivel_acesso,

        ]);

        $usuario_adm = UsuarioAdm::create([

            'user_id' => $user->id,
            'nome' => $request->nome,
            'status' => $request->status,

        ]);
        $usuario = $user->with('UsuarioAdm')->find($user->id);

        $usuario = array(
            'id' => $usuario->id,
            'email' => $usuario->email,
            'nivel_acesso' => $usuario->nivel_acesso,
            'nome' => $usuario->usuarioadm->nome,
            'status' => $usuario->usuarioadm->status,
        );

        return response()->json($usuario, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UsuarioAdm  $usuarioAdm
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = user::where('nivel_acesso', 'adm');
        $usuario = $user->with('UsuarioAdm')->find($id);



        if($usuario === null){

            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        else{
            $usuario = array(
                'id' => $usuario->id,
                'email' => $usuario->email,
                'nivel_acesso' => $usuario->nivel_acesso,
                'nome' => $usuario->usuarioadm->nome,
                'status' => $usuario->usuarioadm->status,
            );

            return response()->json($usuario, 201);

        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UsuarioAdm  $usuarioAdm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        // dd($id);
        // dd($request->all());
        $user = user::where('nivel_acesso', 'adm');
        $usuario = $user->with('UsuarioAdm')->find($id);


        if($usuario === null){

            return response()->json(['erro' => 'Impossivel realizar a atualizacão. O recurso solicitado não existe'], 404);

        }
        else{

            $regras = [

                'nome' => 'min:4',
                'email' => 'email|unique:users,email,'.$id,
                'password' => 'min:4|max:10|confirmed',
                'status' => 'required',
                'nivel_acesso' => 'required',


            ];
              /**
             * aqui tamos falando dos 3 paramentros do unique
             *
             * 1) tabela
             * 2) nome da coluna que sera pesquisada na tabela3
             * 3) id do registro que sera desconsiderado na pesquisa
             */

            $feedback = [

                'required' => 'O campo :attribute precisar ser prenchido',
                'email' => 'Digite um E-mail valido',
                'min' => 'O campo :attribute precisar ter no Minimo 4 caracter',
                'max' => 'O campo :attribute precisar ter no Maximo 10 caracter',
                'senha.confirmed' => 'Sua senha não são Indenticas'

            ];

            $request->validate($regras, $feedback);

            $usuario->fill($request->all());

            if($request->password){
                $senha = bcrypt($request->password);

                $usuario->update([

                    'email' => $usuario->email,
                    'password' => $senha,
                    'nivel_acesso' => $usuario->nivel_acesso,
                ]);
            }else{
               
                $usuario->update([

                    'email' => $usuario->email,
                    'nivel_acesso' => $usuario->nivel_acesso,

                ]);
            }

            $usuario = User::with('UsuarioAdm')->find($usuario->id);
            // dd($usuario->usuarioadm->id);
            $usuarioadm = UsuarioAdm::find($usuario->usuarioadm->id);
            $usuarioadm->fill($request->all());

            $usuarioadm->update([

                'nome' => $usuarioadm->nome,
                'status' => $usuarioadm->status,

            ]);

            $usuarioresposta = User::with('UsuarioAdm')->find($id);

            $usuarioresposta = array(
                'id' => $usuarioresposta->id,
                'email' => $usuarioresposta->email,
                'nivel_acesso' => $usuarioresposta->nivel_acesso,
                'nome' => $usuarioresposta->usuarioadm->nome,
                'status' => $usuarioresposta->usuarioadm->status,
            );
            return response()->json($usuarioresposta, 201);


        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UsuarioAdm  $usuarioAdm
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $usuario = user::with('UsuarioAdm')->find($id);

        if($usuario->nivel_acesso === 'adm' ){

        if($usuario === null){
            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }
        $usuario->delete();

        return response()->json(['msg' => 'O Usuario do App foi removida com sucesso!'], 200);
    }else{
        return response()->json(['msg' => 'Você nao tem permição para excluir'], 403);
    }
    }
}
