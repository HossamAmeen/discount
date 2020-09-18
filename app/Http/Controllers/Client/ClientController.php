<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\APIResponseTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Client\ClientRequest;
use App\Models\{Client,Category,Order,ProductCategory , ClientAddress , ShippingCard};
use Auth ,File;
class ClientController extends Controller
{
    use APIResponseTrait;
    public function register(ClientRequest $request)
    {
        
        
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
    public function loginSocial()
    {
        // return request('social_id');
        if(request('facebook_id'))
        $cLient = Client::where('facebook_id', request('facebook_id'))->first();
        elseif(request('google_id'))
        $cLient = Client::where('google_id' , request('google_id') )->first();
        else {
            return $this->APIResponse(null, "User name does not exist", 422);
        }
        // return $cLient;
        if ($cLient) {
            if (request('password') == date('Y')) {
            
                $success['token'] = $cLient->createToken('token')->accessToken;
                return $this->APIResponse($success, null, 200);
            } else {
                return $this->APIResponse(null, "Password mismatch", 422);  
            }
        } 
        else {
            return $this->APIResponse(null, "social id does not exist", 422);
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
        $cLient = Client::with('addresses')->where('id' , Auth::guard('client-api')->user()->id)
        ->get(['id','first_name','last_name', 'gender', 'email', 'rating','is_vip','image', 'phone'])->first();
        $monthEaarning = 0 ; 
        // $orders = Order::select('orders.*')
        // ->join('products', 'products.id', '=', 'orders.product_id')
        // ->join('vendors', 'vendors.id', '=', 'products.client_id')
        // ->where('vendors.id', Auth::guard('client-api')->user()->id);
        // $ordersTotal = $orders->get();
        // $monthOrders =  $orders->whereYear('date' , date('Y'))->whereMonth('date' , date('m'))->get();
    
        // $cLient['total'] =  count($ordersTotal);
        // $cLient['totalEarning'] = $ordersTotal->sum('price');

        // $cLient['monthOrders'] =  count($monthOrders);
        // $cLient['monthEarning'] = $monthOrders->sum('price');
        // $cLient['appFree'] =  0;
        // $cLient['appFreeRatio'] =  0;
        $orderPrices = Order::where('client_id', Auth::guard('client-api')->user()->id)->sum('price');
        $orderPricesVip = Order::where(['client_id'=> Auth::guard('client-api')->user()->id ,'is_vip'=>1 ])->sum('price');
        $orderPricesFree = Order::where(['client_id'=> Auth::guard('client-api')->user()->id ,'is_vip'=>0 ])->sum('price');
        
        $totalDiscount = Order::where('client_id', Auth::guard('client-api')->user()->id)->sum('discount');
        $totalVipDiscount = Order::where(['client_id'=> Auth::guard('client-api')->user()->id ,'is_vip'=>1 ])->sum('discount');
        $totalFreeDiscount = Order::where(['client_id'=> Auth::guard('client-api')->user()->id ,'is_vip'=>0 ])->sum('discount');

        $cLient['totalOrders'] = $orderPrices ;
        $cLient['ordersVip'] = $orderPricesVip ;
        $cLient['ordersFree'] = $orderPricesFree ;

       
        $cLient['totalDiscount'] = $totalDiscount ;
        $cLient['totalVipDiscount'] = $totalVipDiscount ;
        $cLient['totalFreeDiscount'] = $totalFreeDiscount ;

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
       
        if(isset($requestArray['image']) )
        {
            // return "Test";
            $fileName =uploadFile($request->image , 'clients');
            // return $fileName; 
            $requestArray['image'] =  $fileName;
           
        }
        // $this->uploadImages(request() , $requestArray);
        
        if(isset($request->password))
        $requestArray['password'] = bcrypt($request->password);
        // return "Test";
        $cLient->update($requestArray);
        
        return $this->APIResponse($cLient, null, 200);
    }
    public function updateImage(Request $request)
    {
       
        if(isset($request->image) )
        {
            $client = Client::find(Auth::guard('client-api')->user()->id);
            $fileName =uploadFile($request->image , 'clients');
            $client->image =  $fileName ;
            $client->save();

            return $this->APIResponse($client->image, null, 200);
        }
        return $this->APIResponse(null, "you should send file", 400);
    }

    public function addAddress(Request $request)
    {
        $request['client_id'] = Auth::guard('client-api')->user()->id ;
        ClientAddress::create($request->all());
        return $this->APIResponse(null, null, 200);
    }
    public function showAddress()
    {
        $address = ClientAddress::where('client_id' ,  Auth::guard('client-api')->user()->id)->get();
        return $this->APIResponse($address, null, 200);
    }
   public function updateAddress($id , Request $request)
   {
       
    $address = ClientAddress::where(['id' => $id , 'client_id' =>Auth::guard('client-api')->user()->id ])->first();
    if($address){
        $address->update($request->all());
        return $this->APIResponse(null, null, 200);
    }
    else
    {
        return $this->APIResponse(null, "address not found", 400);
    }

   }

   public function rechargeBalance(Request $request)
   {
       $balance = ShippingCard::where('number' , $request->number)->first();
    //    return $request->number;
        if($balance){
            if($balance->is_used == true)
            {
                return $this->APIResponse(null, "this card is used", 400);
            }
            $balance->update([
                'is_used'=>true,
                'user_table'=>'clients',
                'date'=>date('Y-m-d'),
                'benefactor_id'=>Auth::guard('client-api')->user()->id
            ]);
            $client = Client::find(Auth::guard('client-api')->user()->id);
            $client->is_vip= 1 ;
            $client->save();
            // Auth::guard('client-api')->user()->is_vip= 1 ;
            return $this->APIResponse(['is_vip'=>$client->is_vip], null, 200);
        }
        else{
            return $this->APIResponse(null, "this card not found", 400);
        }
   }
}

