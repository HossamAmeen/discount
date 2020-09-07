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
        return 
        view('back-end.dashboard');
    }
    
}
