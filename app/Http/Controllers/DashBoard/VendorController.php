<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Vendor\UpdateVendorRequest;
use App\Models\Vendor;
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

}
