<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\APIResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Cart , Wishlist ,Order};
use Auth;
class ClientProductController extends Controller
{
    use APIResponseTrait;
    public function showCart()
    {
        $cart = Cart::where('client_id' , Auth::guard('client-api')->user()->id)->where('is_done' , true)->get();
        return $this->APIResponse($cart, null, 200); 
    }
    public function addWishList($productId)
    {
        $wishlist = Wishlist::where(['client_id' =>Auth::guard('client-api')->user()->id,
                                     'product_id'=> $productId
                                     ])
                            ->first();
        if(isset($wishlist)){
            return $this->APIResponse(null, "this product is added", 400);
        }
        else{
            $wishlist = Wishlist::create([
                'client_id' =>Auth::guard('client-api')->user()->id,
                'product_id'=> $productId
            ]);
            return $this->APIResponse(null, null, 200);
        }
       
       
    }
    public function showWishList()
    {
        $wishlist = Wishlist::with('product')->where('client_id' , Auth::guard('client-api')->user()->id)->get(['id','client_id' , 'product_id']);
        return $this->APIResponse($wishlist, null, 200);
    }

    public function showOrders($id = null)
    {
        if($id != null){
            $orders = Order::find($id);
            
            return $this->APIResponse($orders, null, 200);
        }
        else
        {
            $orders = Order::with('address')->where('client_id' ,  Auth::guard('client-api')->user()->id)->get(['id' , 'date','discount' , 'price' ,'status' ,'client_id']);
        return $this->APIResponse($orders, null, 200);
        }
        
    }

    public function updateOrder($id)
    {
        // $order = Order::find($id);
        // $order->rate
        // $order->update([
        //     'rate' , request('rate')
        //     ]);
        // return $this->APIResponse($orders, null, 200);
    }
}
