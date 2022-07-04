<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerUnirController;
use App\Http\Controllers\CadastroUsuariosController;
use App\Http\Controllers\UsuarioAdmController;
use App\Http\Controllers\UsuarioAppController;
use App\Models\UsuarioApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/**
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 * ROUTE OF LOGIN
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 */
Route::post('login', [AuthController::class, 'login'])->name('login');

/**
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 * cadastro de usuarios so de app
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 */
Route::post('cadastro', [CadastroUsuariosController::class, 'cadastro_store'])->name('cadastro.usuarios');

/**
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 * ROUTE OF USER-APP
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 */
Route::middleware('LogUsuarioApp','jwt.auth')->group(function(){

    Route::post('logoutapp', [ AuthController::class, 'logoutUsuarioApp'])->name('logout.usuarioApp');

    //ROUTE OF USER-APP
    //aqui ts os metodos:index,show,store,update,destroy
    Route::apiResource('usuario/app', UsuarioAppController::class);

});

/**
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 * ROUTE OF USER-ADM
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 */
Route::middleware('LogUsuarioAdm','jwt.auth')->group(function(){

    Route::post('logoutadm', [ AuthController::class, 'logoutUsuarioAdm'])->name('logout.usuarioAdm');

    //ROUTE OF USER-ADM
    //aqui ts os metodos:index,show,update,destroy
    Route::apiResource('usuario/adm', UsuarioAdmController::class);


    //ROUTE OF banner
    //aqui ts os metodos:index,show,update,destroy
    Route::apiResource('banner', BannerUnirController::class);
});













