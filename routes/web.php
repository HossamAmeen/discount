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

Route::get('/', function () {
   
   return redirect()->route('login');
   
});
Route::prefix('admin')->namespace('DashBoard')->group(function(){
    Route::middleware('auth')->group(function () {
        Route::get('/' , 'ConfigrationController@index');
        Route::put('update-website-configration/{id}' , 'ConfigrationController@update')->name('configration.update');
        Route::resource('users' , "UserController");
        Route::resource('cities' , "CityController");
        Route::resource('vendors' , "VendorController");
        Route::get('product-categories/{VendorId}' , 'VendorController@showCategoriesOfProducts');
        Route::resource('clients' , "ClientController");
        Route::get('client-carts/{VendorId}' , 'ClientController@showCarts');
        Route::get('client-wishlist/{VendorId}' , 'ClientController@showWishList');
        Route::get('accept-client/{status}/{clientId}/{blockReason}' , 'ClientController@changeStatus');
        Route::resource('categories' , "CategoryController");
        Route::get('admin/categories-products/{VendorId}' , 'VendorController@showCategoriesOfProducts');
        Route::resource('products' , "ProductController");
        Route::resource('orders' , "OrderController");
        Route::resource('offers' , "OfferController");
        Route::resource('cities' , "CityController");
    });
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
