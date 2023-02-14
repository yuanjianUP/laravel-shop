<?php

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
//登陆
Route::get('/',function (){
    return 'this is version v1';
});
Route::post('authorization','App\Http\Controllers\api\LoginController@store')->name('login.store');
Route::get('products','App\Http\Controllers\api\ProductsController@list')->name('products.list');
Route::group(['middleware' => ['auth','verified']],function (){
    Route::get('addressList','App\Http\Controllers\api\UserAddressesController@list')->name('address.list');
});
