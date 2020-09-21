<?php

namespace App\Http\Controllers\CLient;
use App\Http\Controllers\APIResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Cart , Vendor,Order,OrderItem,Product,ProductChoice,OrderChoice};
use Auth;

class CartClientController extends Controller
{
    use APIResponseTrait;
    public function showCart() ///// not work
    {
        $cart =Cart::where(['client_id' => Auth::guard('client-api')->user()->id , 'is_done' => false ])->get();
        return $this->APIResponse($cart, null, 200); 
        
    }
}
