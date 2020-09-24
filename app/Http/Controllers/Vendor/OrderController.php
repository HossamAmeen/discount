<?php

namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\APIResponseTrait;
use App\Models\{Order,Cart,Vendor , OrderChoice , OrderItem};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
class OrderController extends Controller
{
    use APIResponseTrait;
    public function showOrders()
    {
        // $orders = Order::with('choices')
        //                 ->select('orders.*')
        //                 ->join('products', 'products.id', '=', 'orders.product_id')
        //                 ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
        //                 // ->join('buildings', 'buildings.id', '=', 'blocks.building_id')
        //                 // ->where('vendors.id', Auth::guard('vendor-api')->user()->id)
        //                 ->get();
        //  $orders = Order::with('choices')->where('vendor_id' , Auth::guard('vendor-api')->user()->id )
        //                                  ->where('status' ,'!=','done')
        //                                  ->where('status' ,'!=','pending from client')
        //                                  ->where('status' ,'!=','cancelled from vendor')
        //                                  ->orderBy('cart_id')
        //                                  ->get()
        //                                 //  ->sortBy('cart_id')
        //                                  ;
                                        //  ->groupBy('cart_id')
       
        //   $orders = Cart::with('orders')
        //                 ->select('carts.*')
        //                 ->join('orders', 'orders.cart_id', '=', 'carts.id')
        //                 ->where('orders.vendor_id', Auth::guard('vendor-api')->user()->id)
        //                 ->get();
        // return $orders;
        // foreach($cartsId as $order){


        // }
        // $carts = array();
        // foreach($orders as $order){
        //     $cart = array();
        //     foreach($orders as $order2){
        //         if($order2->cart_id == $order->cart_id){
        //             $cart[] = $order;
        //         }
        //     }
        //     $carts[] = $cart ;
        //     $cart = array();
        // }                                
        // return $carts;
        // // return count($orders);
        // for($i=1 ; $i<=count($orders) ; $i++){
        //     return $orders[$i];
        // }
        // // $orders['choices'] = OrderChoice::where('order_id' , );
        $orders = Order::with(['itemsSent.choices','itemsSent.product', 'address','client'])
                            ->where('vendor_id' , Auth::guard('vendor-api')->user()->id )
                                              ->where('status' , '!=' , 'done')
                                              ->where('status' , '!=' , 'accept from vendor')
                                              ->where('status' , '!=' , 'pending from client')
                                              ->get(['id','date' ,'time','price','status','client_address_id','client_id']);
        return $this->APIResponse($orders, null, 200); 
    }
    public function showDoneOrders()
    {
        $oders =Order::with(['items.choices','items.product', 'address','client'])
        ->where('status' ,'done')
        ->where('vendor_id' , Auth::guard('vendor-api')->user()->id )
        ->orderBy('id' , 'DESC')
        ->take(10)
        ->get();
        return $this->APIResponse($oders, null, 200);  
    }
    public function changeStatus($id)
    {
       
        $orderItem = OrderItem::find($id);
        if(isset($orderItem)){
            $status =  request('status') ; 
            $orderItem->update(['status' => $status]);
            $order=Order::find($orderItem->order_id);
            $order->update(['status' => $status]);
            // return $order ;
            return $this->APIResponse(null, null, 200);  
        }
        return $this->APIResponse(null, "this order not found", 400);  
    }
    public function editOrder($id)
    {
        $order = Order::find($id);
        if(isset($order)){
            $quantity =  request('quantity') ; 
            $order->update(['quantity' => $quantity , 'status' => 'edit from vendor']);
            return $this->APIResponse(null, null, 200);  
        }
        return $this->APIResponse(null, "this order not found", 400);  
    }
    public function scanQRCode($orderId)
    {
        $order = Order::where('id' , $orderId)->where('client_id' , request('client_id'))->first();
        if(isset($order)){
            if($order->status == "done" ){
                return $this->APIResponse(null, "the product has used", 400);  
            }
            $order->status = "done";
            $order->save();
            return $this->APIResponse(null, null, 200);  
        }
        return $this->APIResponse(null, "the client not with this product", 400);  
    }
}
