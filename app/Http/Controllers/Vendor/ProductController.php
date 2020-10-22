<?php

namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\APIResponseTrait;
use App\Models\{Product,ProductCategory , ProductChoice , VendorCategory};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Vendor\ProductRequest;
use Auth;
class ProductController extends Controller
{
    use APIResponseTrait;
    public function addProduct(ProductRequest $request)
    {
        
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
       

        if (isset($request->validator) && $request->validator->fails())
        {
            
            return $this->APIResponse(null , $request->validator->messages() ,  400);
        }
        $requestArray = $request->all() ; 
        if($request->category_name){
            $category = ProductCategory::create(['name' => $request->category_name , 'vendor_id'=>Auth::guard('vendor-api')->user()->id ]);
            $requestArray['category_id'] = $category->id;
            VendorCategory::create(['vendor_id'=>Auth::guard('vendor-api')->user()->id , 'category_id' =>$category->id ]);
        }
        else
        VendorCategory::create(['vendor_id'=>Auth::guard('vendor-api')->user()->id , 'category_id' => $request->category_id ]);
        $requestArray['image'] =  $request->image != null ? uploadFile($request->image , 'products') : null;
        $requestArray['vendor_id'] = Auth::guard('vendor-api')->user()->id; 
        $product = Product::create($requestArray);
        if($request->json != null){
        $this->addChoiceForProduct($request->json , $product->id);
        }
       
        return $this->APIResponse(null, null, 200);
    }
    
    public function showProductDetails($id)
    {
        $product = Product::find($id);
        if(!isset($product)){
            return $this->APIResponse(null, "this product not found", 400);
        }
        $product['choices'] = json_encode(ProductChoice::where('product_id' , $id )->get() );//->groupBy('group_name');
        $choices = ProductChoice::where('product_id' , $id )->get()->groupBy('group_name');
        foreach($choices as $key=> $item){
           $data['name'] = $key ; 
           $data['items'] = array();
           foreach($item as $choice)
           {
               $tchoice['id']=$choice->id;
               $tchoice['name']=$choice->name;
               $tchoice['price']=$choice->price;
               $tchoice['quantity']= $choice->quantity;
               $data['items'][] = $tchoice;
           }
           $data['type'] =$choice->type; 
        $choicesArray[] = $data ;
        }
        if( isset($choicesArray))
            $product['choices'] = $choicesArray ;
        else
         $product['choices'] = array();

      
            return $this->APIResponse($product, null, 200);
    }
    public function updateProduct($id ,ProductRequest $request )
    {
        $product = Product::find($id);
        if(isset($product)){
            $requestArray = $request->all() ; 
            $requestArray['image'] =  $request->image != null ? uploadFile($request->image , 'products') : null;
            // $requestArray['image'] =  $request->image != null ? uploadFile($request->image , 'products') : null;
            if($request->category_name){
                $category = ProductCategory::create(['name' => $request->category_name]);
                $requestArray['category_id'] = $category->id ; 
            }
            $product->update($requestArray) ;
            if($request->json != null){
                $product->choices()->delete();
                $this->addChoiceForProduct($request->json , $product->id);
            }
         
            // return $product->choices;
            return $this->APIResponse(null, null, 200);
        }
       
        return $this->APIResponse(null, "this product not found", 400);
    }
    public function addChoiceForProduct($jsonReuest , $productId)
    {

        $json = json_decode($jsonReuest , true) ; 
        // if(!is_array($json)){
        //     return ;
        // }
        $choices = $json['Groups'] ; 
       
        $type;$groupName; 
        if(is_array($choices))
        foreach($choices as $key=> $choiceItems){
           
            foreach($choiceItems as $itemKey =>$item)
            if(is_array($item))
            {
                foreach($item as $itemKeyt =>$itemt){
                  
                    ProductChoice::create([
                        'name' => $itemt['name'] , 
                        'price' => $itemt['price'] , 
                        
                        'type' => $type,
                        'group_name'=>$groupName,
                        'product_id'=>$productId
                    ]);
                }
            }
            else
            {
                if($itemKey == "name")
                {
                    if($item == ""){
                        $groupName = '_'.rand(2 , 10).rand(0,20);
                    }
                    else
                    $groupName = $item ; 
                }
                else
                {
                    
                    $type= $item ; 
                    
                }  
            }
        }
    }
    public function showProducts()
    {
        $products = Product::where('vendor_id' , Auth::guard('vendor-api')->user()->id)
                            ->get(['id' , 'name','description','price','category_id','image','discount_ratio']);
        foreach($products as $product)
            {
                $discount =$product->discount_ratio !=0 ? $product->discount_ratio /3 : (  $vendor->client_ratio ?? 0 * $product->price / 100 ) ; 
                $VIPdiscount =$product->discount_ratio !=0 ? $product->discount_ratio* 2 / 3  : (  $vendor->client_vip_ratio ?? 0 * $product->price / 100 ) ;
                $product['client_price'] = $product->price - $discount ;
                $product['client_vip_price'] = $product->price - $VIPdiscount;
                $favouriteProduct = WishList::where('product_id' , $product->id)->where('client_id' , Auth::guard('client-api')->user()->id)->first();
                $product['is_favourite'] =  $favouriteProduct != null ?1:0;
            }
        return $this->APIResponse($products, null, 200);
    }
    public function showCategories()
    {
        // $categories = ProductCategory::with('products')->get(['id','name']);
        // $categories = ProductCategory::with('products')
        // ->select('product_categories.*')
        // ->join('vendor_categories', 'vendor_categories.category_id', '=', 'product_categories.id')
        // // ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
        // // ->join('buildings', 'buildings.id', '=', 'blocks.building_id')
        // ->where('vendor_categories.vendor_id', Auth::guard('vendor-api')->user()->id)
        // ->get();
        $categories = ProductCategory::with('products')->where('vendor_id' , Auth::guard('vendor-api')->user()->id)->get();
        return $this->APIResponse($categories, null, 200);
    }
    public function showProductCategories()/// not work
    {
        
        return $this->APIResponse(ProductCategory::where('vendor_id' , Auth::guard('vendor-api')->user()->id)->get('name'), null, 200);
    }
}
