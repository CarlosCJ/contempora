<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(UsuarioController::class)->group(function (){

    Route::get('/usuarios', 'getUsers');

    Route::post('/usuarios', 'crearUsuario');

    Route::put('/usuarios', 'updateUserEmail');
    Route::put('/usuarios/{id}', 'updateUser')->whereNumber('id');
    
 });
