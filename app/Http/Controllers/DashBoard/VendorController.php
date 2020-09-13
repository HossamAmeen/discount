<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Vendor\UpdateVendorRequest;
use App\Models\{Vendor,Order,Product};
use Auth;
class VendorController extends BackEndController
{
    public function __construct(Vendor $model)
    {
        parent::__construct($model);
    }
    public function update($id , UpdateVendorRequest $request){

        
       
        $row = $this->model->FindOrFail($id);
        $requestArray = $request->all();
        if(isset($requestArray['password']) && $requestArray['password'] != ""){
            $requestArray['password'] =  Hash::make($requestArray['password']);
        }else{
            unset($requestArray['password']);
        }        
        $requestArray['user_id'] = Auth::user()->id;
        $row->update($requestArray);
        if($request->block_reason){
            $row->block_reason = $request->block_reason;
            $row->save();
        }
        
        session()->flash('action', 'updated successfully');
        return redirect()->route($this->getClassNameFromModel().'.index');
    }
    public function edit($id)
    {
        // return Auth::user()->role;
        $row = $this->model->FindOrFail($id);
        $moduleName = $this->getModelName();
        $pageTitle = "Edit " . $moduleName;
        $pageDes = "Here you can edit " .$moduleName;
        $folderName = $this->getClassNameFromModel();
        $routeName = $folderName;
        $append = $this->appendEdited($id);
        //  return $row->images;

        return view('back-end.' . $folderName . '.edit', compact(
            'row',
            'pageTitle',
            'moduleName',
            'pageDes',
            'folderName',
            'routeName'
        ))->with($append);
    }

    public function appendEdited($vendorId)
    {
        $products = Product::where('vendor_id' , $vendorId)->get();
      
        $orders= Order::whereIn('product_id' , $products->pluck('id') )->get() ;
        $data['orders_count'] =  $orders->count();
        $data['products_count'] = $products ->count() ;
        $data['total_gain'] = $orders ->sum('price') ;
        return $data;
    }
}
