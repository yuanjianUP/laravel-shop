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
      Route::redirect('/','/products')->name('root');
      Route::get('products', 'ProductsController@index')->name('products.index');
      Route::get('products/{product}', 'ProductsController@show')->name('products.show');
      Route::post('cart', 'CartController@add')->name('cart.add');
      Route::get('cart', 'CartController@index')->name('cart.index');
      // 移除购物车商品
      Route::post('orders', 'OrdersController@store')->name('orders.store');
      Route::get('orders','OrdersController@index')->name('orders.index');
      Route::get('orders/{order}','OrdersController@show')->name('orders.show');
});

