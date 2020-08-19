<?php

namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\APIResponseTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\{Vendor,Category,Order,ProductCategory};
use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Vendor\VendorRequest;
use Auth;
class VendorController extends Controller
{
    use APIResponseTrait;
    public function register(VendorRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails())
        {
            return $this->APIResponse(null , $request->validator->messages() ,  400);
        }
        $requestArray = $request->validated();;
        $requestArray['password'] = bcrypt( $request->password);
        
        $this->uploadImages($request , $requestArray);
       
        $vendor = Vendor::create($requestArray);
        $success['token'] = $vendor->createToken('token')->accessToken;
        return $this->APIResponse($success, null, 200);
    }
    public function login()
    {
       
        $validator = Validator::make(request()->all(), [
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);
            ; 
        if ($validator->fails()) {
            return $this->APIResponse(null , $validator->messages() ,  422);
        }
        
        $vendor = Vendor::where($this->checkField(), request('user_name'))->first();

        if ($vendor) {
            if (Hash::check(request('password'), $vendor->password)) {
            
                $success['token'] = $vendor->createToken('token')->accessToken;
                return $this->APIResponse($success, null, 200);
            } else {
                return $this->APIResponse(null, "Password mismatch", 422);  
            }
        } else {
            return $this->APIResponse(null, "User name does not exist", 422);
        }
       
    }
    public function checkField()
    {
        $field = 'phone';

        if (is_numeric( request('user_name'))) {
            $field = 'phone';
        } 
        elseif (filter_var( request('user_name'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        }
        else
        {
            $field = 'user_name';
        }
        return $field ; 
    }
    public function showProfile()
    {
        $vendor = Vendor::find(Auth::guard('vendor-api')->user()->id);
        $monthEaarning = 0 ; 
        $orders = Order::select('orders.*')
        ->join('products', 'products.id', '=', 'orders.product_id')
        ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
        ->where('vendors.id', Auth::guard('vendor-api')->user()->id);
        $ordersTotal = $orders->get();
        $monthOrders =  $orders->whereYear('date' , date('Y'))->whereMonth('date' , date('m'))->get();
       
        $vendor['total'] =  count($ordersTotal);
        $vendor['totalEarning'] = $ordersTotal->sum('price');
    
        $vendor['monthOrders'] =  count($monthOrders);
        $vendor['monthEarning'] = $monthOrders->sum('price');
        $vendor['appFree'] =  0;
        $vendor['appFreeRatio'] =  0;
        return $this->APIResponse($vendor, null, 200);
    }
    
    public function updateProfile(VendorRequest $request)
    {
        
        if (isset($request->validator) && $request->validator->fails())
        {
            return $this->APIResponse(null , $request->validator->messages() ,  422);
        }
        $vendor = Vendor::find(Auth::guard('vendor-api')->user()->id);
        $requestArray = request()->all() ; 
        $this->uploadImages(request() , $requestArray);
        if(isset(request()->password))
        $requestArray['password'] = bcrypt(request()->password);
        $vendor->update($requestArray);
        return $this->APIResponse($vendor, null, 200);
    }
    // public function updateImage(Request $request)
    // {

    //     $vendor = Vendor::find(Auth::guard('vendor-api')->user()->id);
    //     $this->uploadImages(request() , $requestArray);
    //     $vendor->update($requestArray);
    //     return  $vendor->getChanges();
    //     return $this->APIResponse(null, null, 200);
    // }
    public function uploadImages($request ,& $requestArray)
    {
        set_time_limit(8000000);
        $requestArray['store_logo'] =  $request->logo_image != null ? uploadFile($request->logo_image , 'vendors') : null;
        $requestArray['store_background_image'] =  $request->background_image != null ? uploadFile($request->background_image , 'vendors') : null;
        $requestArray['company_registration_image'] =  $request->company_registration_photo != null ? uploadFile($request->company_registration_photo , 'vendors') : null;
        $requestArray['national_id_front_image'] =  $request->national_id_front_photo != null ? uploadFile($request->national_id_front_photo , 'vendors') : null;
        $requestArray['national_id_back_image'] =  $request->national_id_back_image != null ? uploadFile($request->national_id_back_image , 'vendors') : null;
       
    }
    public function logout (Request $request) {

        $token = $request->user()->token();
        $token->revoke();
    
        $response = 'You have been succesfully logged out!';
        return response($response, 200);
    
    }
}
