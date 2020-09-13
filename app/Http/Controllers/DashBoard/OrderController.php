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
        if(request('type') == 'clients')
        $rows = Order::where('client_id' , $id)->paginate(15);
        else
        $rows = Order::where('vendor_id' , $id)->paginate(15);

        $routeName = $this->getClassNameFromModel();
        return view('back-end/orders.index' , compact('rows' ,'routeName' ));
    }
    public function filter($rows)
    {
        if( request('status') != 'null')
        $rows = $rows->where('status' ,request('status'));
        if(request('date') != 'null')
        $rows = $rows->whereDate('created_at', '=', request('date'));
        if(request('is_vip') != 'null')
        $rows = $rows->where('is_vip', '=', request('is_vip'));
        return $rows;
    }
}
