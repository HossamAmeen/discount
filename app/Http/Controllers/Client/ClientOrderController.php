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
                    ->get(['id','total_cost'])
                    ->first();
        // return $cart ;
        if($cart)
        return $this->APIResponse($cart, null, 200); 
        else
        return $this->APIResponse(null, null, 200);
    }
    public function checkoutCart()
    {
        $cart = Cart::where(['client_id' =>  Auth::guard('client-api')->user()->id , 'is_done'=> false ])->first();
        // return request('address_id') ;
        if(isset($cart)){
            if(request('address_id') != null && request('address_id') != 'null'){
                $addressId  =request('address_id') ; 
                // return "Test2";
               
            }else{
                if( Auth::guard('client-api')->user()->favouriteAddress == null ) // ?? Auth::guard('client-api')->user()->addresses->id ;
                {
                    return $this->APIResponse(null, "please choose address", 400);
                }
                else
                {
                    $addressId  =  Auth::guard('client-api')->user()->favouriteAddress->id ;
                    // $addressId  = Auth::guard('client-api')->user()->favouriteAddress->id;
                }
               
                // return "test";
            }
          
        //    return  $addressId ;
           if(count($cart->orders) > 0){
            $orders = Order::where('cart_id' ,$cart->id )->update(['status'=>'sending from client']);
            // return $orders;
            }
            else
            {
                return $this->APIResponse(null, "this cart empty", 400);
            }

            $cart->update(['is_done' =>1 , 'date'=>date('Y-m-d') , 'client_address_id' => $addressId ]);
         
          
            return $this->APIResponse(null, null, 200);
        }
        else{
            return $this->APIResponse(null, "this cart not found", 400);
        }

    }
    public function showOrders($id = null)
    {
        if(request('id') != null){
            $orders = Order::with(['choices','address'])->select('id','quantity','discount','status','price')
            ->find(request('id') );
            
            return $this->APIResponse($orders, null, 200);
        }
        else
        {
            $orders = Order::where('client_id' ,  Auth::guard('client-api')->user()->id)
                                        ->get(['id' ,'discount' , 'price' ,'status' ,'client_id']);
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
        $vendor = Vendor::select('id','discount_ratio','client_ratio','client_vip_ratio')->find($product->vendor_id);
        $is_client_vip = Auth::guard('client-api')->user()->is_vip ; 
        // $discount =  $is_client_vip == true ? $vendor->client_vip_ratio : $vendor->client_ratio ;
        // $orderPrice =  ;
        $order = Order::create([
            'price' =>  $is_client_vip == true ? $product->price - ($vendor->client_vip_ratio *$product->price /100 ) : $product->price -   ($vendor->client_ratio *$product->price /100 ),
            'discount_ratio' =>  $is_client_vip == true ? $vendor->client_vip_ratio : $vendor->client_ratio ,
            'discount' =>  $is_client_vip == true ? $vendor->client_vip_ratio *$product->price /100  : $vendor->client_ratio *$product->price /100 ,
            'is_vip'=>$is_client_vip,
            'quantity' =>$request->quantity ,
            'product_id' => $request->product_id,
            'client_id'=>  $clientId,
            'cart_id'=>$cart->id,
            'vendor_id'=>$vendor->id,
            'vendor_benefit'=>$product->price - ($vendor->discount_ratio * $product->price / 100  )
        ]);
        $choicesCost = $this->addChoiceForOrder($request->json , $order->id);
        $cart->total_cost += $order->quantity * $order->price + $choicesCost ;
        $cart->save();
       
        return $this->APIResponse(null, null, 200);
    }

    public function updateOrder($id)
    {
        // $order = Order::where(['id'=>$id , 'client_id' => Auth::guard('client-api')->user()->id ] )->first();
        $order = Order::find($id);

        if($order)
        {
            if($order->client_id != Auth::guard('client-api')->user()->id)
            return $this->APIResponse(null, "this order not for this client", 400);
            if(request('quantity')){
                $order->quantity = request('quantity');
            }
            if(request('rating')){
                $order->rating = ( $order->rating + request('rating') ) / 2 ;
            }
        $order->save();
        return $this->APIResponse(null, null, 200);
        }
       else
        {
            return $this->APIResponse(null, "this order not found", 400);
        }
       
    }

    public function deleteOrder($orderId)
    {
       $order =  Order::where(['id'=>$orderId])->first();
       if(isset($order)){
           
           if($order->client_id != Auth::guard('client-api')->user()->id){
            return $this->APIResponse(null, "this order not for this client", 400);
           }
           $cart = Cart::find($order->cart_id);
        //    $choicesCost = $this->choicesCost($order->id);
           $choicesCost =  OrderChoice::where('order_id' , $order->id)->get('id');
           $totalChoicesCost =  OrderChoice::where('order_id' , $order->id)->sum('price');
        //    return $choicesCost ;
       
           $cart->total_cost -= $order->price * $order->quantity + $totalChoicesCost ;
            OrderChoice::destroy($choicesCost->toArray());
        // return ;
        //    $choicesCost->delete();
          
           $order->delete();
           $cart->save();
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
        $totalCost = 0 ;
        $type;$groupName; 
        if(is_array($choices))
        foreach($choices as $key=> $choiceItems){
           
            foreach($choiceItems as $itemKey =>$item)
            if(is_array($item))
            {
                foreach($item as $itemKeyt =>$itemt){
                    $totalCost += $itemt['price'] ;
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
        return $totalCost ; 
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
