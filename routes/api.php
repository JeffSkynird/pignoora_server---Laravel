<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

Route::group(['prefix' => 'v1'], function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::post('login', 'App\Http\Controllers\v1\Seguridad\AuthController@login');
        Route::post('login_admin', 'App\Http\Controllers\v1\Seguridad\AuthController@loginAdmin');
        Route::post('logout', 'App\Http\Controllers\v1\Seguridad\AuthController@logout')->middleware('auth:api');
    });
    Route::post('users', 'App\Http\Controllers\v1\Seguridad\UsuarioController@create');
    Route::get('user', 'App\Http\Controllers\v1\Seguridad\UsuarioController@showAuth');
    Route::put('users/{id}', 'App\Http\Controllers\v1\Seguridad\UsuarioController@update');
    Route::get('users/{id}', 'App\Http\Controllers\v1\Seguridad\UsuarioController@show');
    Route::delete('users/{id}', 'App\Http\Controllers\v1\Seguridad\UsuarioController@delete');


    Route::get('print/{id}', 'App\Http\Controllers\v1\Administracion\ResultController@printPdf');
    Route::get('kpis', 'App\Http\Controllers\v1\Reporte\ReportController@kpis');
    Route::get('graph1', 'App\Http\Controllers\v1\Reporte\ReportController@graph1');
    Route::get('graph2', 'App\Http\Controllers\v1\Reporte\ReportController@graph2');


    Route::middleware('auth:api')->group(function () {
        
        Route::post('pay', 'App\Http\Controllers\v1\Administracion\PaymentController@pay');
        
        Route::post('show_payments', 'App\Http\Controllers\v1\Administracion\PaymentController@showPay');
        Route::post('cancel_pawn', 'App\Http\Controllers\v1\Administracion\PawnController@cancelPawn');
        Route::post('acept_driver/{id}', 'App\Http\Controllers\v1\Administracion\PawnController@aceptDriver');
        
        Route::get('user_by_pawn/{id}', 'App\Http\Controllers\v1\Administracion\PawnController@getUserByPawnId');
        Route::post('payments', 'App\Http\Controllers\v1\Administracion\PaymentController@create');

        Route::post('credits', 'App\Http\Controllers\v1\Administracion\CreditController@create');
        Route::get('credits', 'App\Http\Controllers\v1\Administracion\CreditController@showAuth');
        Route::put('credits/{id}', 'App\Http\Controllers\v1\Administracion\CreditController@update');
        Route::get('credits/{id}', 'App\Http\Controllers\v1\Administracion\CreditController@show');
        Route::delete('credits/{id}', 'App\Http\Controllers\v1\Administracion\CreditController@delete');
        
        Route::post('reviews', 'App\Http\Controllers\v1\Administracion\ReviewController@create');
        Route::get('reviews', 'App\Http\Controllers\v1\Administracion\ReviewController@showAuth');
        Route::put('reviews/{id}', 'App\Http\Controllers\v1\Administracion\ReviewController@update');
        Route::get('reviews/{id}', 'App\Http\Controllers\v1\Administracion\ReviewController@show');
        Route::delete('reviews/{id}', 'App\Http\Controllers\v1\Administracion\ReviewController@delete');
        Route::get('reviews_pawn/{id}', 'App\Http\Controllers\v1\Administracion\ReviewController@showByPawnId');

        Route::put('user', 'App\Http\Controllers\v1\Seguridad\UsuarioController@updateAuth');

        Route::get('chats/{id}', 'App\Http\Controllers\v1\Administracion\ChatController@getChat');
        Route::post('chats_user', 'App\Http\Controllers\v1\Administracion\ChatController@sendUser');
        Route::post('chats_admin', 'App\Http\Controllers\v1\Administracion\ChatController@sendAdmin');

        
        Route::get('driver_last_pawns', 'App\Http\Controllers\v1\Administracion\PawnController@getLastDriverPawns');



        Route::get('admin_pawns', 'App\Http\Controllers\v1\Administracion\PawnController@indexAdmin');
        
        Route::get('admin_last_pawns', 'App\Http\Controllers\v1\Administracion\PawnController@getLastAdminPawns');

        Route::get('last_pawns', 'App\Http\Controllers\v1\Administracion\PawnController@getLastPawns');

        Route::get('pawns/{id}', 'App\Http\Controllers\v1\Administracion\PawnController@show');
        
        Route::post('pawn', 'App\Http\Controllers\v1\Administracion\PawnController@create');
        Route::get('pawns', 'App\Http\Controllers\v1\Administracion\PawnController@index');

        Route::post('pawn_images', 'App\Http\Controllers\v1\Administracion\PawnController@createImages');

        Route::delete('pawn_images/{id}', 'App\Http\Controllers\v1\Administracion\PawnController@deleteImages');
        Route::get('users', 'App\Http\Controllers\v1\Seguridad\UsuarioController@index');

        

        Route::get('users_by', 'App\Http\Controllers\v1\Seguridad\UsuarioController@indexBy');

        
        Route::get('super_admin_pawns', 'App\Http\Controllers\v1\Administracion\PawnController@indexSuperAdmin');

        Route::get('super_admin_pawns/{id}', 'App\Http\Controllers\v1\Administracion\PawnController@showSuperAdmin');
        
        Route::get('categories', 'App\Http\Controllers\v1\Administracion\CategoryController@index');
        Route::get('categories/{id}', 'App\Http\Controllers\v1\Administracion\CategoryController@show');
        Route::delete('categories/{id}', 'App\Http\Controllers\v1\Administracion\CategoryController@delete');
        Route::post('categories/{id}', 'App\Http\Controllers\v1\Administracion\CategoryController@update');
        Route::post('categories', 'App\Http\Controllers\v1\Administracion\CategoryController@create');

        
       
    });
});
