<?php

namespace App\Http\Controllers\Dashboard;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Image, Carbon, File;

class BackEndController extends Controller
{

    protected $model;
    protected $routeNameEdit;
    protected $orderBy;
    public function __construct(Model $model , $orderBy = "id")
    {
        $this->model = $model;
        $this->orderBy = $orderBy ; 
    }

    public function getRouteName()
    {
        return   isset($this->routeNameEdit) ? $this->routeNameEdit :  $this->getClassNameFromModel();
    }

    public function index()
    {
        $rows = $this->model;
        $rows = $this->filter($rows);
        $with = $this->with();
        $append = $this->append();
        if(!empty($with)){
            $rows = $rows->with($with);
        }
        $rows = $rows->orderBy($this->orderBy, 'DESC')->paginate(15);
        $moduleName = $this->pluralModelName();
        $sModuleName = $this->getModelName();
        // $routeName = $this->getClassNameFromModel();
        $routeName = $this->getRouteName();
        $pageTitle = "Control ".$moduleName;
        $pageDes = "Here you can add / edit / delete " .$moduleName;
        // return Auth::user()->role;

        return view('back-end.' . $routeName . '.index', compact(
            'rows',

            'pageTitle',
            'moduleName',
            'pageDes',
            'sModuleName',
            'routeName'
        ))->with($append);
    }

    public function create()
    {
        $moduleName = $this->getModelName();
        $pageTitle = "Create ". $moduleName;
        $pageDes = "Here you can create " .$moduleName;
        $folderName = $this->getClassNameFromModel();
        $routeName = $this->getRouteName();
        $append = $this->append();

        // return $append;
        // return  request()->segment(3);
        return view('back-end.' . $folderName . '.create' , compact(
            'pageTitle',
            'moduleName',
            'pageDes',
            'folderName',
            'routeName'
        ))->with($append);
    }

    public function destroy($id)
    {
        $this->model->FindOrFail($id)->delete();
        session()->flash('action', 'deleted successfully');
        $this->deleteRelatedItems($id);
        return redirect()->back();
        return redirect()->route( $this->getRouteName() . '.index');
    }

    public function edit($id)
    {
        // return Auth::user()->role;
        $row = $this->model->FindOrFail($id);
        $moduleName = $this->getModelName();
        $pageTitle = "Edit " . $moduleName;
        $pageDes = "Here you can edit " .$moduleName;
        $folderName = $this->getClassNameFromModel();
        $routeName = $this->getRouteName();
        $append = $this->appendEdited($id);
        //  return $row->images;

        return view('back-end.' . $routeName . '.edit', compact(
            'row',
            'pageTitle',
            'moduleName',
            'pageDes',
            'folderName',
            'routeName'
        ))->with($append);
    }

    public function deleteRelatedItems($rowId)
    {

    }
    protected function uploadImage($request , $height = 400 , $width = 400){

        $photo = $request->file('image');
        $fileName = time().str_random('10').'.'.$photo->getClientOriginalExtension();
        $destinationPath = public_path('uploads/'.$this->getClassNameFromModel().'/');
        // return "test";
        // $image = Image::make($photo->getRealPath())->resize($height, $width);

            // return $destinationPath;

         if(!is_dir($destinationPath) ){
             mkdir($destinationPath);
         }
         $photo->move($destinationPath , $fileName);
        // $image->save($destinationPath.$fileName);
        return 'uploads/'.$this->getClassNameFromModel().'/'. $fileName;
    }

    protected function uploadImage2($request , $height = 400 , $width = 400){

        $photo = $request->file('image');
        $fileName = time().str_random('10').'.'.$photo->getClientOriginalExtension();
        $destinationPath = ('uploads/'.$this->getClassNameFromModel().'/');
        // $image = Image::make($photo->getRealPath())->resize($height, $width);
        $image = Image::make($photo->getRealPath());
            // return $destinationPath;

         if(!is_dir($destinationPath) ){
             mkdir($destinationPath);
         }
        $image->save($destinationPath.$fileName);
        return 'uploads/'.$this->getClassNameFromModel().'/' . $fileName;
    }


    protected function uploadFile($request , $height = 400 , $width = 400){

        $photo = $request->file('image');
        $fileName = time().str_random('10').'.'.$photo->getClientOriginalExtension();
        $destinationPath = public_path('uploads/'.$this->getClassNameFromModel().'/');
        // $image = Image::make($photo->getRealPath())->resize($height, $width);

            // return $destinationPath;

         if(!is_dir($destinationPath) ){
             mkdir($destinationPath);
         }
        $photo->move($destinationPath , $fileName);
        return 'uploads/'.$this->getClassNameFromModel().'/'. $fileName;
    }



    protected function filter($rows)
    {
        return $rows;
    }

    protected function with(){
        return [];
    }

    protected function getClassNameFromModel()
    {
        return strtolower($this->pluralModelName());
    }

    protected function pluralModelName(){
        return str_plural($this->getModelName());
    }

    protected function getModelName(){
        return class_basename($this->model);
    }

    protected function append(){
        return [];
    }
    protected function appendEdited($id){
        return [];
    }
}
