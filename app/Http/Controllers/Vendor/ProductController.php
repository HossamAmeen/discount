<?php

namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\APIResponseTrait;
use App\Models\{Product,Category};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Vendor\ProductRequest;
use Auth;
class ProductController extends Controller
{
    use APIResponseTrait;
    public function addProduct(ProductRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails())
        {
            
            return $this->APIResponse(null , $request->validator->messages() ,  400);
        }
        $requestArray = $request->all() ; 
        $requestArray['image'] =  $request->image != null ? uploadFile($request->image , 'products') : null;
        
        Product::create($requestArray);
        return $this->APIResponse(null, null, 200);
    }
    public function updateProduct($id ,ProductRequest $request )
    {
        $product = Product::find($id);
        if(isset($product)){
            $requestArray = $request->all() ; 
            $requestArray['image'] =  $request->image != null ? uploadFile($request->image , 'products') : null;
            $product->update($requestArray) ;
            return $this->APIResponse(null, null, 200);
        }
       
        return $this->APIResponse(null, "this product not found", 400);
    }
    public function showProducts()
    {
        $products = Product::where('vendor_id' , Auth::guard('vendor-api')->user()->id)
                            ->get(['id' , 'name','description','price','category_id','image']);
        return $this->APIResponse($products, null, 200);
    }
    public function showCategories()
    {
        $products = Product::where('vendor_id' , Auth::guard('vendor-api')->user()->id)
                             ->get(['id' , 'name','description','price','category_id','image']);
        return $this->APIResponse($products, null, 200);
    }
}
