<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\APIResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Cart , WishList ,Vendor,Order , Product , ProductChoice};
use Auth;
class ClientProductController extends Controller
{
    use APIResponseTrait;
    
    public function addWishList($productId)
    {
        $wishlist = WishList::where(['client_id' =>Auth::guard('client-api')->user()->id,
                                     'product_id'=> $productId
                                     ])
                            ->first();
        if(isset($wishlist)){
            return $this->APIResponse(null, "this product is added", 400);
        }
        else{
            $wishlist = WishList::create([
                'client_id' =>Auth::guard('client-api')->user()->id,
                'product_id'=> $productId
            ]);
            return $this->APIResponse(null, null, 200);
        }
       
       
    }
    
    public function showWishList()
    {
        // return "tesT";
        $wishlist = Wishlist::with(['product' ,'product.vendorDiscount' ])->where('client_id' , Auth::guard('client-api')->user()->id)->get(['id','client_id' , 'product_id']);
        return $this->APIResponse($wishlist, null, 200);
    }
    
    public function deleteWishlist($productId)
    {
        $wishlist = WishList::where(['client_id' =>Auth::guard('client-api')->user()->id,
                                     'product_id'=> $productId
                                     ])
                            ->first();
         if(!isset($wishlist)){
            return $this->APIResponse(null, "this product is not found in wishlist", 400);
        }
        else{
            $wishlist->delete();
            return $this->APIResponse(null, null, 200);
        }
    }
    public function showProduct($id)
    {
        $product = Product::with('isFavourite')->find($id);
        // return $product;
      
        if(isset($product)){
            $vendor = Vendor::find($product->vendor_id);
            $product['choices'] = json_encode(ProductChoice::where('product_id' , $id )->get() );//->groupBy('group_name');
            $choices = ProductChoice::where('product_id' , $id )->get()->groupBy('group_name');
            // $choicesArray[] = array();
            foreach($choices as $key=> $item){
               $data['name'] = $key ; 
               $data['items'] = array();
               foreach($item as $choice)
               {
                   $tchoice['id']=$choice->id;
                   $tchoice['name']=$choice->name;
                   $tchoice['price']=$choice->price;
                //    $tchoice['type']=$choice->type;
                   $data['items'][] = $tchoice;
               }
               $data['type'] = $choice->type; 
                $choicesArray[] = $data ;
            }
            if( isset($choicesArray))
                $product['choices'] = $choicesArray ;
            else
                 $product['choices'] = array();
           
            $product['client_price'] = $vendor->client_ratio ;//$product->price - (  $vendor->client_ratio * $product->price / 100 ) ;
            $product['client_vip_price'] =  $vendor->client_vip_ratio;// $product->price - (  $vendor->client_vip_ratio * $product->price / 100 ) ;
            // $is_favourite =  $product->isFavourite() ;   
            // $product['is_favourite'] = $product->isFavourite()  ;
            // return $product->isFavourite() ;
            return $this->APIResponse($product, null, 200);
        }
        else
        {
            return $this->APIResponse(null, "this product not found", 400);
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
