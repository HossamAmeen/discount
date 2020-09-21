<?php

namespace App\Http\Controllers\CLient;
use App\Http\Controllers\APIResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Cart , Vendor,Order,OrderItem,Product,ProductChoice,OrderChoice};
use Auth , DB;

class CartClientController extends Controller
{
    use APIResponseTrait;
    public function addOrder(Request $request)
    {
       
        $clientId = Auth::guard('client-api')->user()->id ; 
        $product= Product::find($request->product_id);
        if(!isset($product)){
            return $this->APIResponse(null, "this product not found", 400);
        }
        $over_quantity = $request->quantity > $product->quantity  ? $request->quantity - $product->quantity : 0 ;
        $vendor = Vendor::select('id','discount_ratio','client_ratio','client_vip_ratio' , 'delivery')->find($product->vendor_id);
        $is_client_vip = Auth::guard('client-api')->user()->is_vip ; 

        $orderItem = OrderItem::where(['product_id'=> $request->product_id , 'client_id' =>  $clientId , 'status'=>'pending from client' ])->first();
        if(isset($orderItem))
        {
            return $this->APIResponse(null, 'this order item is founded', 400);
        }
       
        $orderItem = OrderItem::create([
            'price'=> $is_client_vip == true ? $product->price - ($vendor->client_vip_ratio *$product->price /100 ) : $product->price -   ($vendor->client_ratio *$product->price /100 ),
            'choice_price'=>0,
            'discount'=>$is_client_vip == true ? $vendor->client_vip_ratio *$product->price /100  : $vendor->client_ratio *$product->price /100 ,
            'discount_ratio'=>$is_client_vip == true ? $vendor->client_vip_ratio : $vendor->client_ratio ,
            'vendor_benefit'=>$request->quantity *( $product->price - ($vendor->discount_ratio * $product->price / 100  ) ) ,
            'is_vip'=>$is_client_vip,
            'quantity'=>$request->quantity ,
            'over_quantity'=> $over_quantity,
            'product_id'=>$request->product_id,
           
            'client_id'=> $clientId
        ]);
        // return $orderItem;
        if($request->choices){
            $orderItem->choice_price = $this->addChoiceForOrder($request->choices , $orderItem->id);
            $orderItem->vendor_benefit += $orderItem->choice_price  ;
            $orderItem->save();
           
        }
        return $this->APIResponse(null, null, 200);
    }
    public function addChoiceForOrder($choices , $orderItemId)
    {
       $choices = ProductChoice::whereIn('id' , $choices )->get();
       $totalCost = 0 ; 
       foreach($choices as $choice){
            OrderChoice::create([
                'type'=>$choice->type,
                'name'=>$choice->name,
                'price'=>$choice->price,
                'group_name'=>$choice->group_name,
                'order_item_id'=>$orderItemId
            ]);
            $totalCost +=$choice->price;
       }
       return $totalCost;
    }
    public function showCart() 
    {
        $orderItems = OrderItem::with('product')->where('client_id' , Auth::guard('client-api')->user()->id)
        ->where(['status'=>'pending from client'])
        ->get(['id','product_id' , 'discount_ratio' , 'price' ,'choice_price','over_quantity','quantity']);
        $data['products'] = $orderItems ; 
        // $data['total_cost'] =  $orderItems->sum(\DB::raw('price + choice_price')); //->sum(['price','choice_price']) ; 
        $sql = "SELECT SUM(quantity * price + choice_price) as total_cost FROM order_items WHERE status = 'pending from client' and  client_id = :ID";

        $result = DB::select($sql,['ID'=>Auth::guard('client-api')->user()->id]);
        $data['total_cost'] =$result[0]->total_cost ; 
        // $data['total_cost'] =$result;//  $orderItems->sum('price');
        return $this->APIResponse($data, null, 200); 
    }

    public function checkoutCart()
    {
        // $cart = Cart::where(['client_id' =>  Auth::guard('client-api')->user()->id , 'is_done'=> false ])->first();
        // $orders = Order::where(['client_id' =>  Auth::guard('client-api')->user()->id ,'status' =>'pending from client' ] );

       
            if(request('address_id') != null && request('address_id') != 'null'){
                $addressId  =request('address_id') ;                
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

                // $orders->update([
                //     'status'            => 'sending from client',
                //     'client_address_id' => $addressId,
                //     'date'              => date('Y-m-d'),
                // ]);
                // $order = Order::create([
                //     'date'=>date('Y-m-d'),
                //     'price'=>30,
                //     'delivery_cost','discount_ratio','is_vip','total_discount','vendor_benefit', 'status','client_address_id','vendor_id', 'client_id','cart_id'
                // ]);
                OrderItem::where('client_id' , Auth::guard('client-api')->user()->id )->update([
                    'status'            => 'sending from client',
                ]);
                return $this->APIResponse(null, null, 200);
            }
    }
    
}
