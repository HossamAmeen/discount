<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
class CityController extends BackEndController
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }
}
