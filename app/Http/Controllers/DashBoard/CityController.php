<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use Auth;
class CityController extends BackEndController
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }
    public function store(Request $request){
            $requestArray = $request->all();
            $requestArray['user_id'] = Auth::user()->id;
            $this->model->create($requestArray);
            session()->flash('action', 'add successfully');     
            return redirect()->route($this->getClassNameFromModel().'.index');
        }
}
