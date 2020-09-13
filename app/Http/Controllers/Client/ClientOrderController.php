<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\APIResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Cart , Vendor,Order,Product ,OrderChoice};
use Auth;
class ClientOrderController extends Controller
{
    use APIResponseTrait;
    public function showCart()
    {
        $cart = Cart::with('orders.product')->where('client_id' , Auth::guard('client-api')->user()->id)->where('is_done' , false)
                    // ->get('id','price','quabtity','discount')
                    ->first();
        // return $cart ;
        if($cart)
        return $this->APIResponse($cart->orders, null, 200); 
        else
        return $this->APIResponse(null, null, 200);
    }
    public function checkoutCart($cartId)
    {
        $cart = Cart::where(['id'=>$cartId , 'client_id' =>  Auth::guard('client-api')->user()->id , 'is_done'=> false ])->first();
        // return request('address_id') ;
        if(isset($cart)){
            $cart->update(['is_done' =>1 , 'date'=>date('Y-m-d') , 'client_address_id' =>request('address_id') ]);
            return $this->APIResponse(null, null, 200);
        }
        else{
            return $this->APIResponse(null, "this cart not found", 400);
        }

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
    public function addOrder(Request $request)
    {
        $clientId = Auth::guard('client-api')->user()->id ; 
        $product = Product::find($request->product_id);
        if(!isset($product)){
            return $this->APIResponse(null, "this product not found", 400);
        }
        $cart = Cart::where(  'client_id' , '=' ,  $clientId )->where( 'is_done' , false)->first();
        if(!isset($cart)){
            $cart = Cart::create(['client_id' =>  $clientId ,'total_cost'=>0 ]);
        }
        $order = Order::where(['product_id'=> $request->product_id , 'client_id' =>  $clientId , 'cart_id' => $cart->id])->first();
        // return $order ;
        if(isset($order))
        {
        
            return $this->APIResponse(null, 'this order is found in the cart', 400);
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
            'client_id'=>  $clientId,
            'cart_id'=>$cart->id,
        ]);
        $cart->total_cost += $order->quantity * $order->price ;
        $cart->save();
        $this->addChoiceForOrder($request->json , $order->id);
        return $this->APIResponse(null, null, 200);
    }

    public function updateOrder($id)
    {
        $order = Order::where(['id'=>$id , 'client_id' => Auth::guard('client-api')->user()->id ] )->first();
            if(request('quantity')){
                $order->quantity = request('quantity');
            }
            if(request('rating')){
                $order->rating = ( $order->rating + request('rating') ) / 2 ;
            }
        $order->save();
        return $this->APIResponse(null, null, 200);
    }

    public function deleteOrder($orderId)
    {
       $order =  Order::where(['id'=>$orderId , 'client_id' => Auth::guard('client-api')->user()->id])->first();
       if(isset($order)){
           $order->delete();
        return $this->APIResponse(null, null, 200);
       }
       else{
        return $this->APIResponse(null, "this order not found", 400);
       }
    }
    public function addChoiceForOrder($jsonReuest , $productId)
    {
        $json = json_decode($jsonReuest , true) ; 
       
        $choices = $json['Groups'] ; 
       
        $type;$groupName; 
        if(is_array($choices))
        foreach($choices as $key=> $choiceItems){
           
            foreach($choiceItems as $itemKey =>$item)
            if(is_array($item))
            {
                foreach($item as $itemKeyt =>$itemt){
                  
                    OrderChoice::create([
                        'name' => $itemt['name'] , 
                        'price' => $itemt['price'] , 
                        'type' => $type,
                        'group_name'=>$groupName,
                        'order_id'=>$productId
                    ]);
                }
            }
            else
            {
                if($itemKey == "name")
                {
                   
                    $groupName = $item ; 
                }
                else
                {
                    
                    $type= $item ; 
                    
                }  
            }
        }
    }

     /**
         * 
         * {
                
                "Groups" : [
                                {
                                    "name":"size",
                                    "type":1,
                                    "items" : [
                                        {
                                        "name":"small",
                                        "price":12
                                        },
                                        {
                                        "name":"med",
                                        "price":15
                                        },
                                        {
                                        "name":"large",
                                        "price":20
                                        }
                                    ]
                                },
                                {
                                    "name":"second choice ",
                                    "type":2,
                                     "items" : [
                                        {
                                        "name":"small",
                                        "price":12
                                        },
                                        {
                                        "name":"med",
                                        "price":15
                                        },
                                        {
                                        "name":"large",
                                        "price":20
                                        }
                                    ]
                                }
                        ]
            }

         */
}
