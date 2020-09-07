<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
class VendorController extends BackEndController
{
    public function __construct(Vendor $model)
    {
        parent::__construct($model);
    }
}
