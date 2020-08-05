<?php

namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\APIResponseTrait;
use Illuminate\Support\Facades\Hash;
use App\Models\{Vendor,Category};
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
        $requestArray = $request->all();
        $requestArray['password'] = bcrypt( $request->password);
        if($request->category_name){
            Category::create(['name' => $request->category_name]);
        }
        $this->uploadImages($request , $requestArray);
       
        $vendor = Vendor::create($requestArray);
        $success['token'] = $vendor->createToken('token')->accessToken;
        return $this->APIResponse($success, null, 200);
    }
    public function login()
    {
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
        return $this->APIResponse($vendor, null, 200);
    }
    public function updateProfile()
    {
        $vendor = Vendor::find(Auth::guard('vendor-api')->user()->id);
        $requestArray = request()->all() ; 
        $this->uploadImages(request() , $requestArray);
        $requestArray['password'] = bcrypt(request()->password);
        $vendor->update($requestArray);
        return $this->APIResponse(null, null, 200);
    }
    public function uploadImages($request ,& $requestArray)
    {
        set_time_limit(8000000);
        $requestArray['store_logo'] =  $request->logo_image != null ? uploadFile($request->logo_image , 'vendors') : null;
        $requestArray['store_background_image'] =  $request->background_image != null ? uploadFile($request->background_image , 'vendors') : null;
        $requestArray['company_registration_image'] =  $request->company_registration_photo != null ? uploadFile($request->company_registration_photo , 'vendors') : null;
        $requestArray['national_id_front_image'] =  $request->national_id_front_photo != null ? uploadFile($request->national_id_front_photo , 'vendors') : null;
        $requestArray['national_id_back_image'] =  $request->national_id_back_image != null ? uploadFile($request->national_id_back_image , 'vendors') : null;
       
    }
}
