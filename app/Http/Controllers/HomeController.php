<?php

namespace App\Http\Controllers;
use App\Http\Controllers\APIResponseTrait;
use App\Models\{City,Category};
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use APIResponseTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function showCities()
    {
        $data = City::all();
        return $this->APIResponse($data, null, 200);
    }
    public function showCategories()
    {
        $data = Category::all();
        return $this->APIResponse($data, null, 200);
    }
}
