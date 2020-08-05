<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->namespace('DashBoard')->group(function(){

    Route::post('/login', 'APIAuthController@login')->name('admin.login');
    Route::middleware('checkLogin')->group(function () {
        Route::post('/logout', 'APIAuthController@logout')->name('admin.logout');
    });
    Route::middleware('cors')->group(function () {
        Route::resource('admins' , "AdminController");
        Route::resource('teachers' , "TeacherController");
        Route::resource('students' , "StudentController");
        Route::resource('rooms' , "RoomController");
        Route::resource('filesrooms' , "FileRoomController");
        Route::post('upload-file', 'UploadFileController@uploadFile');
    });
    
   
});

        //////////////// vender ///////////////////
Route::prefix('vendor')->namespace('Vendor')->group(function(){
    Route::post('/register', 'VendorController@register');
    Route::post('/login', 'VendorController@login');
    Route::middleware('checkLogin:vendor-api')->group(function () {
        Route::get('/show-profile', 'VendorController@showProfile');
        Route::put('/update-profile', 'VendorController@updateProfile');
        Route::post('/update-store', 'VendorController@updateProfile');
            /////////// products ////////////
        Route::post('/add-product', 'ProductController@addProduct');
        Route::put('/update-product/{id}', 'ProductController@updateProduct');
        Route::get('/show-products', 'ProductController@showProducts');
        Route::get('/show-categories', 'ProductController@showCategories');
        
    });
});

Route::get('cities', 'HomeController@showCities');
Route::get('categories', 'HomeController@showCategories');