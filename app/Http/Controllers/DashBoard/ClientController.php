<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Client,WishList,Cart};
class ClientController extends BackEndController
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }
    public function changeStatus($status , $clientID , $blockReason)
    {
        
        $client = Client::find($clientID);
        $client->status = $status ; 
        if($status == "blocked"){
          $client->block_reason = $blockReason ; 
        }
        else
        {
            $client->block_reason = "not found";
        }
        $client->save();
        return json_encode(['status'=>'secuss']);
    }
    public function showWishlist($clientId)
    {
        
        $rows = WishList::where('client_id' , $clientId)->paginate(15);
        $folderName = $this->getClassNameFromModel();
        $pageTitle = "Show wishlist";
        $routeName = "Show wishlist";
        return view('back-end.' . $folderName . '.show-wishlist', compact(
            'rows',
            'routeName',
            'pageTitle',
            'folderName',
        ));
    }
    public function showCarts($clientId)
    {
        $rows = Cart::where('client_id' , $clientId)->paginate(15);
        $folderName = $this->getClassNameFromModel();
        $pageTitle = "Show carts";
        $routeName = "Show carts";
        return view('back-end.' . $folderName . '.show-carts', compact(
            'rows',
            'routeName',
            'pageTitle',
            'folderName',
        ));
    }
}
