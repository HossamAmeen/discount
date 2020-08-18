<?php

namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\APIResponseTrait;
use App\Models\{Order,Vendor , OrderChoice};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
class OrderController extends Controller
{
    use APIResponseTrait;
    public function showOrders()
    {
        $orders = Order::with('choices')
                        ->select('orders.*')
                        ->join('products', 'products.id', '=', 'orders.product_id')
                        ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
                        // ->join('buildings', 'buildings.id', '=', 'blocks.building_id')
                        ->where('vendors.id', Auth::guard('vendor-api')->user()->id)
                        ->get();
        // $orders['choices'] = OrderChoice::where('order_id' , );
        return $this->APIResponse($orders, null, 200); 
    }
    public function showDoneOrders()
    {
        $oders = Order::with(['client','product'])
        ->where('status' ,'done')
        ->orderBy('id' , 'DESC')
        ->take(10)
        ->get();
        return $this->APIResponse($oders, null, 200);  
    }
    public function changeStatus($id)
    {
       
        $order = Order::find($id);
        if(isset($order)){
            $status =  request('status') ; 
            $order->update(['status' => $status]);
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
