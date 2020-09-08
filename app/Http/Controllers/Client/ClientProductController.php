<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\APIResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Cart , Wishlist ,Vendor,Order , Product , ProductChoice};
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

   

    public function showProduct($id)
    {
        $product = Product::find($id);
        
      
        if(isset($product)){
            $vendor = Vendor::find($product->vendor_id);
            $product['choices'] = json_encode(ProductChoice::where('product_id' , $id )->get() );//->groupBy('group_name');
            $choices = ProductChoice::where('product_id' , $id )->get()->groupBy('group_name');
            foreach($choices as $key=> $item){
               $data['name'] = $key ; 
               $data['items'] = array();
               foreach($item as $choice)
               {
                   $tchoice['id']=$choice->id;
                   $tchoice['name']=$choice->name;
                   $tchoice['price']=$choice->price;
                   $data['items'][] = $tchoice;
               }
                $choicesArray[] = $data ;
            }
            $product['choices'] = $choicesArray ;
           
            $product['client_price'] = $vendor->client_ratio ;//$product->price - (  $vendor->client_ratio * $product->price / 100 ) ;
            $product['client_vip_price'] =  $vendor->client_vip_ratio;// $product->price - (  $vendor->client_vip_ratio * $product->price / 100 ) ;
            return $this->APIResponse($product, null, 200);
        }
        else
        {
            return $this->APIResponse(null, "this product not found", 400);
        }
       
       
       
        
    }
    
    public function addOrder(Request $request)
    {
        $product = Product::find($request->product_id);
        if(!isset($product)){
            return $this->APIResponse(null, "this product not found", 400);
        }
        $cart = Cart::where(  'client_id' , '=' , Auth::guard('client-api')->user()->id )->where( 'is_done' , false)->first();
        if(!isset($cart)){
            $cart = Cart::create(['client_id' => Auth::guard('client-api')->user()->id , 'date'=>date('Y-m-d') ]);
        }
        $vendor = Vendor::select('client_ratio','client_vip_ratio')->find($product->vendor_id);
        $is_client_vip = Auth::guard('client-api')->user()->is_vip ; 
        // $discount =  $is_client_vip == true ? $vendor->client_vip_ratio : $vendor->client_ratio ;
        $order = Order::create([
            'price' => $is_client_vip == true ? $product->price - (  $vendor->client_vip_ratio * $product->price / 100 ) : $product->price - (  $vendor->client_ratio * $product->price / 100 ),
            'discount' =>  $is_client_vip == true ? $vendor->client_vip_ratio : $vendor->client_ratio ,
            'is_vip'=>$is_client_vip,
            'quantity' =>$request->quantity ,
            'product_id' => $request->product_id,
            'client_id'=> Auth::guard('client-api')->user()->id,
        ]);
        return $this->APIResponse(null, null, 200);
    }
    public function showOrders($id = null)
    {
        if($id != null){
            $orders = Order::with(['choices','address'])->select('id','quantity','date','discount','status','price','client_address_id')->find($id);
            
            return $this->APIResponse($orders, null, 200);
        }
        else
        {
            $orders = Order::with('address')->where('client_id' ,  Auth::guard('client-api')->user()->id)->get(['id' , 'date','discount' , 'price' ,'status' ,'client_id']);
        return $this->APIResponse($orders, null, 200);
        }
        
    }
    public function checkoutCart($cartId)
    {
        $cart = Cart::find($id);
        if(isset($cart)){
            $cart->update(['is_done' =>1 ]);
        }
        else{
            return $this->APIResponse(null, "this cart not found", 400);
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
