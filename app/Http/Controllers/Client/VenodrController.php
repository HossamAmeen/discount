<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\APIResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Client,Vendor,Category,Order,ProductCategory};
use Auth;
class VenodrController extends Controller
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
       
        $vendors = $vendors->get(['id','first_name', 'last_name' ,'client_ratio', 'client_vip_ratio' ,'rating', 'store_description', 'store_logo' ]);
        return $this->APIResponse($vendors, null, 200);      
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
