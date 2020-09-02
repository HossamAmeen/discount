<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\APIResponseTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Client\ClientRequest;
use App\Models\{Client,Category,Order,ProductCategory};
use Auth;
class ClientController extends Controller
{
    use APIResponseTrait;
    public function register(ClientRequest $request)
    {
        $path = public_path()."uploads/vendors/".date("Y-m-d");
        if(!File::isDirectory($path))
        {
            File::makeDirectory($path, 0777, true, true);
        }   
        copy (  public_path().'/avatar.png',  $path.'/avatar2.png' );

        
        if (isset($request->validator) && $request->validator->fails())
        {
            return $this->APIResponse(null , $request->validator->messages() ,  400);
        }
        $requestArray = $request->validated();
        $requestArray['password'] = bcrypt( $request->password);
      
        $cLient = Client::create($requestArray);
        $success['token'] = $cLient->createToken('token')->accessToken;
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
        
        $cLient = Client::where($this->checkField(), request('user_name'))->first();

        if ($cLient) {
            if (Hash::check(request('password'), $cLient->password)) {
            
                $success['token'] = $cLient->createToken('token')->accessToken;
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
        $cLient = Client::where('id' , Auth::guard('client-api')->user()->id)->get(['first_name','last_name', 'gender', 'email', 'rating', 'phone'])->first();
        $monthEaarning = 0 ; 
        // $orders = Order::select('orders.*')
        // ->join('products', 'products.id', '=', 'orders.product_id')
        // ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
        // ->where('vendors.id', Auth::guard('vendor-api')->user()->id);
        // $ordersTotal = $orders->get();
        // $monthOrders =  $orders->whereYear('date' , date('Y'))->whereMonth('date' , date('m'))->get();
    
        // $cLient['total'] =  count($ordersTotal);
        // $cLient['totalEarning'] = $ordersTotal->sum('price');

        // $cLient['monthOrders'] =  count($monthOrders);
        // $cLient['monthEarning'] = $monthOrders->sum('price');
        // $cLient['appFree'] =  0;
        // $cLient['appFreeRatio'] =  0;
        return $this->APIResponse($cLient, null, 200);
    }

    public function updateProfile(ClientRequest $request)
    {
        
        if (isset($request->validator) && $request->validator->fails())
        {
            return $this->APIResponse(null , $request->validator->messages() ,  422);
        }
        $cLient = Client::find(Auth::guard('client-api')->user()->id);
        $requestArray = $request->validated();
        // $this->uploadImages(request() , $requestArray);
        if(isset(request()->password))
        $requestArray['password'] = bcrypt(request()->password);
        $cLient->update($requestArray);
        return $this->APIResponse($cLient, null, 200);
    }
}

