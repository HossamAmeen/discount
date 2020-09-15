<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends BackEndController
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
    public function show($VendorId)
    {
        $rows = Product::where('vendor_id' , $VendorId)->paginate(15);
        $routeName = $this->getClassNameFromModel();
        return view('back-end/products.index' , compact('rows' ,'routeName' ));
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
        $append = $this->append();
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
    public function filter($rows)
    {
        if( request('search') != null )
        $rows = $rows->where('name' , 'LIKE', '%' . request('search') . '%' )
                     ->orWhere('description' , 'LIKE', '%' . request('search') . '%' );
        return $rows; 
    }
}
