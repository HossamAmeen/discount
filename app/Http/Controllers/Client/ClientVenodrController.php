<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\APIResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Client,Vendor,Category,Order,Product,ProductCategory , Offer};
use Auth;
class ClientVenodrController extends Controller
{
    use APIResponseTrait;
    public function showVendors()
    {
        $vendors = Vendor::accepted();//get(['id','first_name', 'last_name' ,'client_ratio', 'client_vip_ratio' ,  'store_description', 'store_logo' ]);
        // $vendors = 
        if(request('city_id')){
            $vendors = Vendor::where('city_id' , request('city_id'));
                                                        
        }
        if(request('category_id')){
            $vendors = $vendors ->where('category_id' , request('category_id'));
        }
        // if(request('city_id')){}
       
        $vendors = $vendors->get(['id','first_name', 'last_name' ,'client_ratio', 'client_vip_ratio' ,'rating', 'store_description', 'store_logo' , 'store_background_image' ]);
        return $this->APIResponse($vendors, null, 200);      
    }

    public function showOffers()
    {
        $offers = Offer::get('image');
        return $this->APIResponse($offers, null, 200);      
    }
    public function showVendorsCategories($id)
    {
        $categories = ProductCategory::with('products')->where('vendor_id' , $id)->get();
        $products = Product::where('vendor_id' , $id)->get(['id' , 'name','description','price','category_id','image']);
        $data = array();
        $vendor = Vendor::select('first_name','last_name','client_ratio','client_vip_ratio','store_logo','rating')->find($id);
        foreach($categories as $item)
        {
            foreach($item->products as $product)
            {
                $product['client_price'] = $product->price - (  $vendor->client_ratio * $product->price / 100 ) ;
                $product['client_vip_price'] = $product->price - (  $vendor->client_vip_ratio * $product->price / 100 ) ;
            }
            // $product[$item->name] = $item ;
            // // $product[$item->name][] = $item->products ;
           
            // $data[]=$product;
            // $product['client_price'] = 0;
            
        }
        return $this->APIResponse($categories, null, 200);    
    }
    public function showProducts($id)
    {
        $products = Product::where('vendor_id' , $id)->get(['id' , 'name','description','price','category_id','image']);
        $data = array();
        $vendor = Vendor::select('first_name','last_name','client_ratio','client_vip_ratio','store_logo','rating')->find($id);
        foreach($products as $item)
        {
            $product = $item ;
            $product['client_price'] = $item->price - (  $vendor->discount_ratio / 3 * $item->price / 100 ) ;
            $product['client_vip_price'] = $item->price - (  ( $vendor->discount_ratio * 2 / 3 )* $item->price / 100 ) ;
            $data[]=$product;
            
        }
        // $data[]=  $vendor ;
        // $data['vendor'] =$vendor ; // Vendor::select('first_name','rating')->find($id);
        return $this->APIResponse($data, null, 200);
    }
    public function searchOfVendors()
    {
        // return request('name') ;
        $vendors = Vendor::accepted()->where('first_name' , 'LIKE', '%' . request('name') . '%' )
                                     ->orWhere('last_name' , 'LIKE', '%' . request('name') . '%')
                                    ->get();
        return $this->APIResponse($vendors, null, 200);   
    }

}
