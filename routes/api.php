<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::prefix('admin')->namespace('DashBoard')->group(function(){

//     Route::post('/login', 'APIAuthController@login')->name('admin.login');
//     Route::middleware('checkLogin')->group(function () {
//         Route::post('/logout', 'APIAuthController@logout')->name('admin.logout');
//     });
//     Route::middleware('cors')->group(function () {
//         Route::resource('admins' , "AdminController");
//         Route::resource('teachers' , "TeacherController");
//         Route::resource('students' , "StudentController");
//         Route::resource('rooms' , "RoomController");
//         Route::resource('filesrooms' , "FileRoomController");
//         Route::post('upload-file', 'UploadFileController@uploadFile');
//     });
    
   
// });

        //////////////// vender ///////////////////
Route::prefix('vendor')->namespace('Vendor')->group(function(){
    Route::any('/register', 'VendorController@register');
    Route::post('/login', 'VendorController@login');
    Route::post('/logout', 'VendorController@logout');
    Route::middleware('checkLogin:vendor-api')->group(function () {
        Route::get('/show-profile', 'VendorController@showProfile');
        Route::post('/update-profile', 'VendorController@updateProfile');
        Route::post('/update-store', 'VendorController@updateProfile');
        Route::post('update-store-image', 'VendorController@updateProfile');
        Route::get('product-categories', 'VendorController@showProductCategories');
        Route::put('recharge-balance' , 'VendorController@rechargeBalance');
            /////////// products ////////////
        Route::post('/add-product', 'ProductController@addProduct');
        Route::post('/update-product/{id}', 'ProductController@updateProduct');
        Route::get('/show-products', 'ProductController@showProducts');
        Route::get('/show-product/{id}', 'ProductController@showProductDetails');
        Route::get('/show-categories', 'ProductController@showCategories');
            ////////// orders //////////////// 
            Route::middleware('checkIsAccept:vendor-api')->group(function () {
                Route::get('/show-orders', 'OrderController@showOrders');    
                Route::get('/show-done-orders', 'OrderController@showDoneOrders');
                Route::get('/change-status-order/{id}', 'OrderController@changeStatus');
                Route::put('/edit-order/{id}', 'OrderController@editOrder');
                Route::get('/scan-qrcode/{order_id}', 'OrderController@scanQRCode');
            });
    });
});

Route::prefix('client')->namespace('Client')->group(function(){
        Route::post('register', 'ClientController@register');
        Route::post('/login', 'ClientController@login');
        Route::post('/login-social', 'ClientController@loginSocial');
        Route::post('/logout', 'ClientController@logout');
    Route::middleware('checkLogin:client-api')->group(function () {
        Route::get('/show-profile', 'ClientController@showProfile');
        Route::get('/show-address', 'ClientController@showAddress');
        Route::post('/add-address', 'ClientController@addAddress');
        Route::put('/update-address/{addressId}', 'ClientController@updateAddress');
        Route::put('/update-profile', 'ClientController@updateProfile');
        Route::post('/update-image', 'ClientController@updateImage');
      
        Route::middleware('checkIsAccept:client-api')->group(function () {
            Route::get('show-wishlist' , 'ClientProductController@showWishList');
            Route::post('add-wishlist/{productId}' , 'ClientProductController@addWishList');
            Route::delete('delete-wishlist/{productId}' , 'ClientProductController@deleteWishlist');
            Route::get('detial-product/{productId}' , 'ClientProductController@showProduct');
            
            Route::get('show-cart' , 'CartClientController@showCart'); 
            Route::put('checkout-cart' , 'CartClientController@checkoutCart');  
            Route::get('/show-orders/{orderId?}', 'ClientOrderController@showOrders'); ///// لسه  show with choices
            Route::post('add-order' , 'CartClientController@addOrder'); 
            Route::put('/update-order/{orderId?}', 'ClientOrderController@updateOrder');    ////// لسه choice
            Route::delete('delete-order/{orderId}' , 'ClientOrderController@deleteOrder'); 
            Route::delete('delete-order-item/{orderId}' , 'ClientOrderController@deleteOrderItem'); 

            Route::get('show-vendors' , 'ClientVenodrController@showVendors');
            Route::get('show-vendors-categories/{vendorId}' , 'ClientVenodrController@showVendorsCategories');
            Route::get('show-vendor-products/{vendorId}', 'ClientVenodrController@showProducts');
            Route::get('offers' , 'ClientVenodrController@showOffers');
            Route::get('search' , 'ClientVenodrController@searchOfVendors');
            Route::put('recharge-balance' , 'ClientController@rechargeBalance');
        }); 
    });
});
Route::get('cities', 'HomeController@showCities');
Route::get('categories', 'HomeController@showCategories');
Route::get('configration', 'HomeController@configration');
Route::get('questions', 'HomeController@showQuestions');
Route::post('complaint', 'HomeController@addComplaint');


