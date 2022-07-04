<?php

namespace App\Http\Controllers;

use App\Models\BannerUnir;
use App\Http\Requests\StoreBannerUnirRequest;
use App\Http\Requests\UpdateBannerUnirRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BannerUnirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $limit = $request->limit;
        $banner = BannerUnir::paginate($limit);

        return response()->json($banner, 201);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBannerUnirRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $regras = [
            // 'banner_nome' => 'required|min:4',
            'caminho_arquivo' => 'required|file|mimes:png,jpg',
            'status' => 'required',
        ];
        $feedback = [
            'required' => 'O campo :attribute Precisar ser prenchido',
            // 'min' => 'O campo :attribute Precisar ter no minimo 4 Caracter',
            'mimes' => 'O formato do arquivo de ser PNG OU JPG',
        ];

        $request->validate($regras, $feedback);

        $imagem = base64_encode(file_get_contents($request->file('caminho_arquivo')));

        $nome = $request->caminho_arquivo;

        $consulta = auth()->user();
        $user = $consulta->id;

        $data = date("Y-m-d");
        $ano = explode('-', $data);


        $response = Http::withBasicAuth(env('AZURE_S3_USER'), env('AZURE_S3_PASSWORD'))->post('https://storage.audax.mobi/storage/blob', [
            'container' => 'unir',
            'client' => 'banner',
            'year' =>  $ano[0],
            'blobs' => [
                [
                    'name' => $nome->getClientOriginalName(),
                    'base64' => $imagem
                ]
            ]
        ]);


        $banner = BannerUnir::create([

            'user_id' => $user,
            'banner_nome' => $response->object()->blobs[0]->name,
            'caminho_arquivo' => $response->object()->blobs[0]->url,
            'status' => $request->status

        ]);

        return response()->json($banner, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BannerUnir  $bannerUnir
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $banner = BannerUnir::find($id);

        if ($banner === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }

        return response()->json($banner, 201);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBannerUnirRequest  $request
     * @param  \App\Models\BannerUnir  $bannerUnir
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        //
        $regras = [
            // 'banner_nome' => 'required|min:4',
            'caminho_arquivo' => 'file|mimes:png,jpg',
            'status' => 'required',
        ];
        $feedback = [
            'required' => 'O campo :attribute Precisar ser prenchido',
            // 'min' => 'O campo :attribute Precisar ter no minimo 4 Caracter',
            'mimes' => 'O formato do arquivo deve ser PNG OU JPG',
        ];

        $request->validate($regras, $feedback);

        $banner = BannerUnir::find($id);

        $resposta =  auth()->user();
        $user = $resposta->id;

        if ($banner) {

            if ($request->caminho_arquivo) {

                $imagem = base64_encode(file_get_contents($request->file('caminho_arquivo')));

                $nome = $request->caminho_arquivo;

                $data = date("Y-m-d");
                $ano = explode('-', $data);

                //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
                //deletar
                //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
                $response = Http::withBasicAuth(env('AZURE_S3_USER'), env('AZURE_S3_PASSWORD'))->delete('https://storage.audax.mobi/storage/blob', [
                    'container' => 'unir',
                    'client' => 'banner',
                    'year' =>  $ano[0],
                    'blobName' => $banner->banner_nome
                ]);

                //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
                //criar
                //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
                $response = Http::withBasicAuth(env('AZURE_S3_USER'), env('AZURE_S3_PASSWORD'))->post('https://storage.audax.mobi/storage/blob', [
                    'container' => 'unir',
                    'client' => 'banner',
                    'year' =>  $ano[0],
                    'blobs' => [
                        [
                            'name' => $nome->getClientOriginalName(),
                            'base64' => $imagem
                        ]
                    ]
                ]);


                $banner->update([

                    'user_id' => $user,
                    'banner_nome' => $response->object()->blobs[0]->name,
                    'caminho_arquivo' => $response->object()->blobs[0]->url,
                    'status' => $request->status

                ]);

                return response()->json($banner, 201);

            } else {

                $dados = $banner->fill($request->all());


                $banner->update([

                    'user_id' => $user,
                    'banner_nome' => $dados->banner_nome,
                    'caminho_arquivo' => $dados->caminho_arquivo,
                    'status' => $dados->status

                ]);

                return response()->json($banner, 201);

            }
        } else {

            return response()->json(['erro' => 'Impossivel realizar a atualizacão. O recurso solicitado não existe'], 404);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BannerUnir  $bannerUnir
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = BannerUnir::find($id);

        if($banner === null){
            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }else{
            //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
            //deletar
            //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
            $data = date("Y-m-d");
            $ano = explode('-', $data);

            $response = Http::withBasicAuth(env('AZURE_S3_USER'), env('AZURE_S3_PASSWORD'))->delete('https://storage.audax.mobi/storage/blob', [
                'container' => 'unir',
                'client' => 'banner',
                'year' =>  $ano[0],
                'blobName' => $banner->banner_nome
            ]);

            $banner->delete();

            return response()->json(['msg' => 'Banner Excluido com Sucesso'], 201);
        }
    }
}
