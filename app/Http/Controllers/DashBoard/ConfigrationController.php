<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configration;
class ConfigrationController extends BackEndController
{
    public function __construct(Configration $model)
    {
        parent::__construct($model);
    }
    public function index()
    {
        $configrationSite = Configration::find(1);
        $configrationSite['vendors'] = \App\Models\Vendor::count();
        $configrationSite['clients'] = \App\Models\Client::count();
        $configrationSite['orders'] = \App\Models\Order::count();
        return view('back-end.dashboard' , compact('configrationSite'));
    }
    public function update(Request $request , $id)
    {
        $configration = Configration::find(1);
        $configration->update($request->all());
        session()->flash('action', 'add successfully');
        return redirect()->back();

    }
    
}
