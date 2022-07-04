<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsuarioApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsuarioAppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //


            $user = user::where('nivel_acesso', 'app');
            $usuario = $user->with('UsuarioApp')->get();


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

        dd('storeapp');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UsuarioApp  $usuarioApp
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = user::where('nivel_acesso', 'app');
        $usuario = $user->with('UsuarioApp')->find($id);


        if($usuario === null){

            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        else{

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
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UsuarioApp  $usuarioApp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
// dd($id);
        $user = user::where('nivel_acesso', 'app');
        $usuario = $user->with('UsuarioApp')->find($id);

        // dd($usuario);

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
                'foto_usuario' => 'file|mimes:png,jpg'

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
                'foto_usuario.mimes' => 'O formato do arquivo de ser PNG OU JPG',
                'senha.confirmed' => 'Sua senha não são Indenticas'

            ];

            $request->validate($regras, $feedback);

            if($request->file('foto_usuario')){
                Storage::disk('public')->delete($usuario->usuarioapp->foto_usuario);// aqui estou removendo a imagem do meu disco

                $imagem = $request->file('foto_usuario');
                $imagem_urn = $imagem->store('imagens', 'public');// o metodo store tem 2 paramentro o 1 e o path = caminho que vai ser armazenado, no caso o nome da pasta. o 2 paramentro e chamado de disco onde vamos armazena e nos configura isso em config no arquivo filesystems.

                $usuario->fill($request->all());

                if($request->password){

                    $senha = bcrypt($request->password);

                    $usuario->update([

                        'email' => $usuario->email,
                        'password' => $senha,
                        'nivel_acesso' => $usuario->nivel_acesso,

                    ]);
                }

                $usuario->update([

                    'email' => $usuario->email,
                    'nivel_acesso' => $usuario->nivel_acesso,

                ]);

                $usuario = User::with('UsuarioApp')->find($usuario->id);
                // dd($usuario->usuarioadm->id);
                $usuarioapp = UsuarioApp::find($usuario->usuarioapp->id);
                $usuarioapp->fill($request->all());

                $usuarioapp->update([
                    'nome' => $usuarioapp->nome,
                    'sobrenome' => $usuarioapp->sobrenome,
                    'status' => $usuarioapp->status,
                    'foto_usuario' => $imagem_urn

                ]);

                $usuarioresposta = User::with('UsuarioApp')->find($id);

                $usuarioresposta = array(
                    'id' => $usuarioresposta->id,
                    'email' => $usuarioresposta->email,
                    'nivel_acesso' => $usuarioresposta->nivel_acesso,
                    'nome' => $usuarioresposta->usuarioapp->nome,
                    'sobrenome' => $usuarioresposta->usuarioapp->sobrenome,
                    'status' => $usuarioresposta->usuarioapp->status,
                    'foto_usuario' => $usuarioresposta->usuarioapp->foto_usuario,
                );

                return response()->json($usuarioresposta, 201);
            }

            $usuario->fill($request->all());

             if($request->password){

                    $senha = bcrypt($request->password);

                    $usuario->update([

                        'email' => $usuario->email,
                        'password' => $senha,
                        'nivel_acesso' => $usuario->nivel_acesso,

                    ]);
                }

                $usuario->update([

                    'email' => $usuario->email,
                    'nivel_acesso' => $usuario->nivel_acesso,

                ]);

            $usuario = User::with('UsuarioApp')->find($usuario->id);
                // dd($usuario->usuarioadm->id);
                $usuarioapp = UsuarioApp::find($usuario->usuarioapp->id);
                $usuarioapp->fill($request->all());

            $usuarioapp->update([
                'nome' => $usuarioapp->nome,
                'sobrenome' => $usuarioapp->sobrenome,
                'status' => $usuarioapp->status,

            ]);



            $usuarioresposta = User::with('UsuarioApp')->find($id);

            $usuarioresposta = array(
                'id' => $usuarioresposta->id,
                'email' => $usuarioresposta->email,
                'nivel_acesso' => $usuarioresposta->nivel_acesso,
                'nome' => $usuarioresposta->usuarioapp->nome,
                'sobrenome' => $usuarioresposta->usuarioapp->sobrenome,
                'status' => $usuarioresposta->usuarioapp->status,
                'foto_usuario' => $usuarioresposta->usuarioapp->foto_usuario,
            );

            return response()->json($usuarioresposta, 201);


        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UsuarioApp  $usuarioApp
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $usuario = user::with('UsuarioApp')->find($id);


        if($usuario === null){
            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }else{

        //remover o arquivo caso um novo arquivo tenha sido enviado no request
        Storage::disk('public')->delete($usuario->usuarioapp->foto_usuario);// aqui estou removendo a imagem do meu disco


        $usuario->delete();

        return response()->json(['msg' => 'O Usuario do App foi removida com sucesso!'], 204);

        }


    }
}
