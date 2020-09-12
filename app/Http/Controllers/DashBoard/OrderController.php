<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
class OrderController extends BackEndController
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
    public function show($id)
    {
        // return request('type');
        $rows = Order::where('client_id' , $id)->paginate(15);
        $routeName = $this->getClassNameFromModel();
        return view('back-end/orders.index' , compact('rows' ,'routeName' ));
    }
}
