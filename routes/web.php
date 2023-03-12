<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'PagesController@root')->name('root');

Auth::routes();

Route::group(['middleware' => ['auth']],function() {
      Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
      // 创建收货地址
      Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
      Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
      // 编辑收货地址
      Route::get('user_addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
});

