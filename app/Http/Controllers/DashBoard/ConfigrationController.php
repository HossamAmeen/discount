<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Configration,Complaint};
use Auth;
class ConfigrationController extends BackEndController
{
    public function __construct(Configration $model)
    {
        parent::__construct($model);
    }
    public function index()
    {
        // return date('m');
        $configrationSite = Configration::find(1);
        $vendors = \App\Models\Vendor::query();
        $clients = \App\Models\Client::query();
        $orders  =\App\Models\Order::query();
        $products  =\App\Models\Product::query();
        $top_product = \App\Models\OrderItem::select('product_id')
                ->selectRaw('count(product_id) as qty')
                ->groupBy('product_id')
                ->orderBy('qty', 'DESC')
                ->limit(1)
                ->get();
        $top_product =\App\Models\Product::find($top_product[0]->product_id);
        $configrationSite['vendors'] =$vendors->count() ;
        // $configrationSite['accepted_vendors'] =$vendors->where('status' , 'accept')->count() ;
        // $configrationSite['blocked_vendors'] =$vendors->where('status' , 'blocked')->count() ;
        // return  $configrationSite['blocked_vendors'] ;
        $configrationSite['current_month_vendors'] =$vendors->whereMonth('created_at' , date('m'))->count() ;
        $configrationSite['clients'] = $clients->count();
        $configrationSite['today_clients'] = $clients->wheredate('created_at' , date('Y-m-d'))->count();
        $configrationSite['orders'] = $orders->count();
        $configrationSite['today_orders'] =  $orders->wheredate('created_at' , date('Y-m-d'))->count();
        $configrationSite['products'] = $products->count();
        // return $top_product;
        if(isset($top_product))
            $configrationSite['top_product'] = $top_product->name. '('. ( $top_product->vendor->store_name ??  " " ).')';
        else
            $configrationSite['top_product'] = "";


            // $top_product = \App\Models\Order::pluck('product_id')->toArray();
            // $occurrences = array_count_values($top_product);
            // // arsort($occurrences);
            // $items = array_slice($occurrences, 0, 3);
            // return $occurrences;
        // return $ordered[0]->product->name ;
        return view('back-end.dashboard' , compact('configrationSite'));
    }
    public function update(Request $request , $id)
    {
        $configration = Configration::find(1);
        $request['user_id'] = Auth::user()->id;
        $configration->update($request->all());
        // return $configration;
        session()->flash('action', 'updated successfully');
        return redirect()->back();

    }
    public function showComlpaints()
    {
        $rows = Complaint::paginate(15);
        $folderName ='complaints';
        $pageTitle = "Show complaints from clients";
        $routeName = "complaints";
        return view('back-end.complaints', compact(
            'rows',
            'routeName',
            'pageTitle',
            'folderName',
        ));
    }
    public function deleteComlpaint($complaintId)
    {
        $row = Complaint::find($complaintId);
        if($row){
            $row->delete();
            session()->flash('action', 'deleted successfully');
        }
        return redirect()->back();
    }
}
