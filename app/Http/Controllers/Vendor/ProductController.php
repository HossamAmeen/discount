<?php

namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\APIResponseTrait;
use App\Models\{Product,ProductCategory , ProductChoice};
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
            $category = ProductCategory::create(['name' => $request->category_name]);
            $requestArray['category_id'] = $category->id ; 
        }
        $requestArray['image'] =  $request->image != null ? uploadFile($request->image , 'products') : null;
        $requestArray['vendor_id'] = Auth::guard('vendor-api')->user()->id; 
        $product = Product::create($requestArray);

        $this->addChoiceForProduct($request->json , $product->id);
       
       
        return $this->APIResponse(null, null, 200);
    }
    
    public function showProductDetails($id)
    {
        $product = Product::find($id);
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
               $data['items'][] = $tchoice;
           }
            $choicesArray[] = $data ;
        }
        $product['choices'] = $choicesArray ;
        if(isset($product)){
            return $this->APIResponse($product, null, 200);
        }
       
        return $this->APIResponse(null, "this product not found", 400);
    }
    public function updateProduct($id ,ProductRequest $request )
    {
        $product = Product::find($id);
        if(isset($product)){
            $requestArray = $request->all() ; 
            $requestArray['image'] =  $request->image != null ? uploadFile($request->image , 'products') : null;
            $requestArray['image'] =  $request->image != null ? uploadFile($request->image , 'products') : null;
            if($request->category_name){
                $category = ProductCategory::create(['name' => $request->category_name]);
                $requestArray['category_id'] = $category->id ; 
            }
            $product->update($requestArray) ;
            $product->choices()->delete();
            $this->addChoiceForProduct($request->json , $product->id);
            // return $product->choices;
            return $this->APIResponse(null, null, 200);
        }
       
        return $this->APIResponse(null, "this product not found", 400);
    }
    public function addChoiceForProduct($jsonReuest , $productId)
    {
        $json = json_decode($jsonReuest , true) ; 
       
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
                            ->get(['id' , 'name','description','price','category_id','image']);
        return $this->APIResponse($products, null, 200);
    }
    public function showCategories()
    {
        $categories = ProductCategory::with('products')->get(['id','name']);
        return $this->APIResponse($categories, null, 200);
    }
}
