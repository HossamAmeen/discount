<?php

namespace App\Http\Controllers;
use App\Http\Controllers\APIResponseTrait;
use App\Models\{City,Category};
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use APIResponseTrait;
    public function showCities()
    {
        
        return $this->APIResponse(City::get(['id','name']), null, 200);
    }
    public function showCategories()
    {
       
        return $this->APIResponse(Category::get(['id','name']), null, 200);
    }
}
