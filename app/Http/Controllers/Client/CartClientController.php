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
        $discountRatio = $product->discount_ratio != 0 ? $product->discount_ratio : ($is_client_vip == true ? $vendor->client_vip_ratio : $vendor->client_ratio );
        $discount =  $discountRatio * $product->price /100;
        $vendorBenefit = $request->quantity * ( $product->discount_ratio != 0 ? $product->discount_ratio  : $vendor->discount_ratio * $product->price / 100 );
        $orderItem = OrderItem::create([
            'price'=> $product->price - $discount,
            'choice_price'=>0,
            'discount'=> $discount,
            'discount_ratio'=>$discountRatio ,
            'vendor_benefit'=>$vendorBenefit ,
            'is_vip'=>$is_client_vip,
            'quantity'=>$request->quantity ,
            'over_quantity'=> $over_quantity,
            'product_id'=>$request->product_id,
            'vendor_id'=>$vendor->id,
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
        // $sql = "SELECT SUM(quantity * price + choice_price) as total_cost FROM order_items 
        // WHERE status = 'pending from client'
        
        //  and  client_id = :ID";

        // $result = DB::select($sql,['ID'=>Auth::guard('client-api')->user()->id]);
        // // return $result[0]->total_cost;
        // if($result[0]->total_cost != null)
        // $data['total_cost'] =$result[0]->total_cost ; 
        // else
        // $data['total_cost'] = 0 ; 

        $total_cost = OrderItem::where('client_id' , Auth::guard('client-api')->user()->id)
        ->where(['status'=>'pending from client'])
                ->value(DB::raw("SUM(quantity * price + choice_price)"));
        // return $stats;
        $data['total_cost'] = (integer)$total_cost ?? 0 ;
        // $data['total_cost'] =$result;//  $orderItems->sum('price');
        return $this->APIResponse($data, null, 200); 
    }

    public function checkoutCart()
    {
            if(request('address_id') != null && request('address_id') != 'null'){
                $addressId  =request('address_id') ;                
            }
            else{
                if( Auth::guard('client-api')->user()->favouriteAddress == null ) // ?? Auth::guard('client-api')->user()->addresses->id ;
                {
                    return $this->APIResponse(null, "please choose address", 400);
                }
                else
                {
                    $addressId  =  Auth::guard('client-api')->user()->favouriteAddress->id ;
                   
                }
            }
             
                $vendors_id= OrderItem::where(['client_id' => Auth::guard('client-api')->user()->id , 'status'=>'pending from client' ])
                                        ->pluck('vendor_id');
                $orderItems = OrderItem::where(['client_id' => Auth::guard('client-api')->user()->id , 'status'=>'pending from client' ])
                                        ->get();
                // return  array_unique($vendors_id->toArray() );
                // return  ($vendors_id->toArray() );
                // $vendors_id = array_unique($vendors_id->toArray());
                $vendors = array_unique($vendors_id->toArray());
                foreach($vendors as $vendor_id)
                {
                   $vendor = Vendor::find($vendor_id);
                //    return $vendor_id;
                   $order  = Order::create([
                    'date'=>date('Y-m-d'),
                    'time'=>date("h:i"),
                    'price'=>0,
                    'delivery_cost'=>$vendor->delivery ?? 0,
                    'discount_ratio'=>$vendor->discount_ratio,
                    'is_vip'=>Auth::guard('client-api')->user()->is_vip,
                    'total_discount'=>0,
                    'vendor_benefit'=>0,
                    'status'=>'sending from client',
                    'client_address_id'=> $addressId,
                    'vendor_id'=>$vendor->id,
                    'client_id'=>Auth::guard('client-api')->user()->id,
                   ]);

                   $orderPrice = 0;
                   $orderTotaldiscount=0;
                    // return "Test";
                   foreach($orderItems as $orderItem){
                    // return $order->vendor_id ;
                       if($orderItem->vendor_id ==  $order->vendor_id)
                       {
                            $orderItem->update([
                                'status'   => 'sending from client',
                                'order_id' => $order->id,
                            ]); 
                        
                            $orderPrice += $orderItem->price * $orderItem->quantity + $orderItem->choice_price;
                            $orderTotaldiscount += $orderItem->discount;

                            $order->vendor_benefit +=   $orderItem->vendor_benefit ;
                       }
                       
                   }
                   $order->price = $orderPrice +  $order->delivery_cost  ; //$order->itemsSent->sum('price') +  $order->delivery_cost;
                   $order->total_discount = $order->itemsSent->sum('discount');
                   $order->save();
                }
                // foreach($orderItems as $orderItem){
                       
                //     // if($orderItem->vendor_id ==  $order->vendor_id)
                //     {
                //      $orderItem->update([
                //          'status'   => 'sending from client',
                //         //  'order_id' => $order,
                //      ]); 
                //     //  $orderPrice += $orderItem->price;
                //     //  $orderTotaldiscount += $orderItem->discount;
                //     }
                    
                //  }
               if(count($orderItems) == 0){
                return $this->APIResponse(null, "this cart is empty", 400);
               }
                return $this->APIResponse(null, null, 200);
            
    }

    public function proccesingCart()
    {
        $data['favouriteAddress']= Auth::guard('client-api')->user()->favouriteAddress ;
        $vendors_id= OrderItem::where(['client_id' => Auth::guard('client-api')->user()->id , 'status'=>'pending from client' ])
        ->pluck('vendor_id');
        $totalPrice = OrderItem::where(['client_id' => Auth::guard('client-api')->user()->id , 'status'=>'pending from client' ])
                ->get()->sum('price');
        $totaldiscount = OrderItem::where(['client_id' => Auth::guard('client-api')->user()->id , 'status'=>'pending from client' ])
                ->get()->sum('discount');
        $totalShipping = Vendor::whereIn('id' , $vendors_id->toArray())->get()->sum('delivery');
        $data['totalDiscount']=$totaldiscount;
        $data['total']=  $totalPrice ;
        $data['shipping']=$totalShipping ;
        return $this->APIResponse($data, null, 200);
    }
    public function deleteOrderItem($orderItemId)
    {
        
        $orderItem =  OrderItem::find($orderItemId);
       if(isset($orderItem)){
        //    return $orderItem;
            if( $orderItem->client_id != Auth::guard('client-api')->user()->id){
                return $this->APIResponse(null, "this order item not for this client", 400);
            }
           $totalChoicesCost =  OrderChoice::where('order_item_id' , $orderItem->id)->sum('price');
           $choicesCost =OrderChoice::where('order_item_id' , $orderItem->id)->get('id');
           OrderChoice::destroy($choicesCost->toArray());
           $orderItem->delete();
           return $this->APIResponse(null, null, 200);
        }
        else{
            return $this->APIResponse(null, "this order item not found", 400);
           }
    }
}
